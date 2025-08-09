<nav class="fixed inset-y-0 left-0 z-50 bg-white/10 backdrop-blur-sm transition-all duration-500 ease-in-out shadow-2xl border-r border-gradient-to-b from-blue-200/50 to-blue-400/50" 
     :class="{'w-16': !sidebarExpanded, 'w-60': sidebarExpanded}" 
     x-cloak>
    
    <!-- Logo -->
    <div class="flex items-center justify-center h-16 border-b border-gray-200/50">
        <a href="{{ route('admin.auth.dashboard') }}" class="flex items-center justify-center group relative overflow-hidden">
            <img src="/images/library.png" alt="Library Logo" width="30" height="30" class="transition-transform duration-300 group-hover:scale-110">
            <span x-show="sidebarExpanded" class="ml-2 text-gray-800 font-semibold text-lg tracking-tight animate-fadeInUp" 
                  x-transition:enter="ease-out duration-300" 
                  x-transition:enter-start="opacity-0 translate-y-2" 
                  x-transition:enter-end="opacity-100 translate-y-0">
                Library
            </span>
            <span class="absolute inset-0 bg-blue-400 opacity-0 group-hover:opacity-10 rounded-full animate-ripple"></span>
        </a>
    </div>

    <!-- Main Navigation -->
    <div class="mt-4 grid gap-1">
        <!-- Dashboard Link -->
        <x-nav-link :href="route('admin.auth.dashboard')" :active="request()->routeIs('admin.auth.dashboard')" class="flex items-center px-5 py-3 group relative overflow-hidden">
            <svg class="h-5 w-5 text-gray-600 group-hover:text-blue-500 group-hover:scale-110 group-hover:rotate-12 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span x-show="sidebarExpanded" class="ml-3 text-gray-800 font-medium tracking-tight animate-fadeInUp" 
                  x-transition:enter="ease-out duration-300" 
                  x-transition:enter-start="opacity-0 translate-y-2" 
                  x-transition:enter-end="opacity-100 translate-y-0"
                  :class="{'text-blue-600': {{ request()->routeIs('admin.auth.dashboard') ? 'true' : 'false' }}}">
                Dashboard
            </span>
            <span class="absolute inset-0 bg-blue-500 opacity-0 group-hover:opacity-10 rounded-md animate-ripple"></span>
            <span class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-blue-200 to-blue-400 transition-opacity duration-300" 
                  :class="{'opacity-100': {{ request()->routeIs('admin.auth.dashboard') ? 'true' : 'false' }}, 'opacity-0': !{{ request()->routeIs('admin.auth.dashboard') ? 'true' : 'false' }}}"></span>
        </x-nav-link>

        <!-- Students Link -->
        <x-nav-link :href="route('admin.students.index')" :active="request()->routeIs('admin.students.index')" class="flex items-center px-5 py-3 group relative overflow-hidden">
            <svg class="h-5 w-5 text-gray-600 group-hover:text-blue-500 group-hover:scale-110 group-hover:rotate-12 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 005.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span x-show="sidebarExpanded" class="ml-3 text-gray-800 font-medium tracking-tight animate-fadeInUp" 
                  x-transition:enter="ease-out duration-300" 
                  x-transition:enter-start="opacity-0 translate-y-2" 
                  x-transition:enter-end="opacity-100 translate-y-0"
                  :class="{'text-blue-600': {{ request()->routeIs('admin.students.index') ? 'true' : 'false' }}}">
                Students
            </span>
            <span class="absolute inset-0 bg-blue-500 opacity-0 group-hover:opacity-10 rounded-md animate-ripple"></span>
            <span class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-blue-200 to-blue-400 transition-opacity duration-300" 
                  :class="{'opacity-100': {{ request()->routeIs('admin.students.index') ? 'true' : 'false' }}, 'opacity-0': !{{ request()->routeIs('admin.students.index') ? 'true' : 'false' }}}"></span>
        </x-nav-link>

        <!-- Attendance Link -->
        <x-nav-link :href="route('admin.attendance.index')" :active="request()->routeIs('admin.attendance.*')" class="flex items-center px-5 py-3 group relative overflow-hidden">
            <svg class="h-5 w-5 text-gray-600 group-hover:text-blue-500 group-hover:scale-110 group-hover:rotate-12 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span x-show="sidebarExpanded" class="ml-3 text-gray-800 font-medium tracking-tight animate-fadeInUp" 
                  x-transition:enter="ease-out duration-300" 
                  x-transition:enter-start="opacity-0 translate-y-2" 
                  x-transition:enter-end="opacity-100 translate-y-0"
                  :class="{'text-blue-600': {{ request()->routeIs('admin.attendance.*') ? 'true' : 'false' }}}">
                Attendance
            </span>
            <span class="absolute inset-0 bg-blue-500 opacity-0 group-hover:opacity-10 rounded-md animate-ripple"></span>
            <span class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-blue-200 to-blue-400 transition-opacity duration-300" 
                  :class="{'opacity-100': {{ request()->routeIs('admin.attendance.*') ? 'true' : 'false' }}, 'opacity-0': !{{ request()->routeIs('admin.attendance.*') ? 'true' : 'false' }}}"></span>
        </x-nav-link>

        <!-- Books Link -->
        <x-nav-link :href="route('admin.books.index')" :active="request()->routeIs('admin.books.index')" class="flex items-center px-5 py-3 group relative overflow-hidden">
            <svg class="h-5 w-5 text-gray-600 group-hover:text-blue-500 group-hover:scale-110 group-hover:rotate-12 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <span x-show="sidebarExpanded" class="ml-3 text-gray-800 font-medium tracking-tight animate-fadeInUp" 
                  x-transition:enter="ease-out duration-300" 
                  x-transition:enter-start="opacity-0 translate-y-2" 
                  x-transition:enter-end="opacity-100 translate-y-0"
                  :class="{'text-blue-600': {{ request()->routeIs('admin.books.index') ? 'true' : 'false' }}}">
                Books
            </span>
            <span class="absolute inset-0 bg-blue-500 opacity-0 group-hover:opacity-10 rounded-md animate-ripple"></span>
            <span class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-blue-200 to-blue-400 transition-opacity duration-300" 
                  :class="{'opacity-100': {{ request()->routeIs('admin.books.index') ? 'true' : 'false' }}, 'opacity-0': !{{ request()->routeIs('admin.books.index') ? 'true' : 'false' }}}"></span>
        </x-nav-link>

        <!-- Borrow Requests Link -->
        <x-nav-link :href="route('admin.borrow.requests')" :active="request()->routeIs('admin.borrow.requests')" class="flex items-center px-5 py-3 group relative overflow-hidden">
            <svg class="h-5 w-5 text-gray-600 group-hover:text-blue-500 group-hover:scale-110 group-hover:rotate-12 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span x-show="sidebarExpanded" class="ml-3 text-gray-800 font-medium tracking-tight animate-fadeInUp" 
                  x-transition:enter="ease-out duration-300" 
                  x-transition:enter-start="opacity-0 translate-y-2" 
                  x-transition:enter-end="opacity-100 translate-y-0"
                  :class="{'text-blue-600': {{ request()->routeIs('admin.borrow.requests') ? 'true' : 'false' }}}">
                Borrow Requests
            </span>
            <span class="absolute inset-0 bg-blue-500 opacity-0 group-hover:opacity-10 rounded-md animate-ripple"></span>
            <span class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-blue-200 to-blue-400 transition-opacity duration-300" 
                  :class="{'opacity-100': {{ request()->routeIs('admin.borrow.requests') ? 'true' : 'false' }}, 'opacity-0': !{{ request()->routeIs('admin.borrow.requests') ? 'true' : 'false' }}}"></span>
        </x-nav-link>
    </div>
<!-- User Profile Section (Bottom) -->
    <div class="absolute bottom-0 left-0 right-0 border-t border-gray-200/50 p-4 bg-white/10 backdrop-blur-sm">
        <div class="relative">
            <x-dropdown x-data="{ open: false }" x-cloak>
                <x-slot name="trigger">
                    <button @click.stop="open = !open" 
                            class="flex items-center w-full text-left focus:outline-none hover:bg-blue-100/20 rounded-md transition-all duration-300 p-2 group relative overflow-hidden"
                            aria-haspopup="true" 
                            :aria-expanded="open">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 rounded-full bg-gray-200/50 p-1 text-gray-600 group-hover:text-blue-500 group-hover:scale-110 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 1115 0H4.5z"></path>
                            </svg>
                        </div>
                        <div x-show="sidebarExpanded" class="ml-3 overflow-hidden transition-all duration-300 ease-in-out animate-fadeInUp">
                            <div class="text-sm font-medium text-gray-800 truncate">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</div>
                        </div>
                        <div x-show="sidebarExpanded" class="ml-auto transition-transform duration-300" :class="{ 'rotate-180': open }">
                            <svg class="h-4 w-4 text-gray-600 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                        <span class="absolute inset-0 bg-blue-500 opacity-0 group-hover:opacity-10 rounded-md animate-ripple"></span>
                    </button>
                </x-slot>
                <x-slot name="content">
                    <div class="absolute bg-white backdrop-blur-sm rounded-lg shadow-2xl z-50 overflow-hidden transition-all duration-300 ease-out w-64 p-2"
                         :style="sidebarExpanded ? 'left: calc(15rem + 0.5rem); bottom: 1rem' : 'right: calc(-16rem - 0.5rem); bottom: 1rem; left: auto'"
                         x-show="open"
                         @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="transform opacity-0 translate-x-10"
                         x-transition:enter-end="transform opacity-100 translate-x-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="transform opacity-100 translate-x-0"
                         x-transition:leave-end="transform opacity-0 translate-x-10">
                        <div class="flex flex-col space-y-1">
                            <x-dropdown-link :href="route('profile.edit')"
                                             class="flex items-center space-x-2 text-sm text-gray-800 px-3 py-2 rounded transition-all duration-200 hover:bg-blue-100/50 hover:text-blue-500">
                                <svg class="w-5 h-5 text-gray-600 hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 1115 0H4.5z"></path>
                                </svg>
                                <span class="text-gray-800">{{ __('Profile') }}</span>
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                                 onclick="event.preventDefault(); this.closest('form').submit();"
                                                 class="flex items-center space-x-2 text-sm text-gray-800 hover:bg-blue-100/50 hover:text-blue-600 px-3 py-2 rounded transition-all duration-200">
                                    <svg class="w-5 h-5 text-gray-600 hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3-3h-8.25m0 0l3-3m-3 3l3 3"></path>
                                    </svg>
                                    <span class="text-gray-800">{{ __('Log Out') }}</span>
                                </x-dropdown-link>
                            </form>
                        </div>
                    </div>
                </x-slot>
            </x-dropdown>
        </div>
    </div>

    <!-- Toggle Button -->
    <button @click="sidebarExpanded = !sidebarExpanded" 
            class="absolute -right-4 top-6 bg-white/90 backdrop-blur-sm border border-gray-200/50 rounded-full p-2 shadow-lg hover:bg-blue-100/50 hover:shadow-xl transition-all duration-300 transform hover:scale-110">
        <svg class="h-5 w-5 text-gray-600 hover:text-blue-500 transition-transform duration-300" 
             :class="{ 'rotate-180': sidebarExpanded }" 
             fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
    </button>
</nav>

<style>
    @keyframes ripple {
        0% { transform: scale(0); opacity: 0.3; }
        100% { transform: scale(4); opacity: 0; }
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-ripple {
        animation: ripple 0.6s ease-out;
    }
    .animate-fadeInUp {
        animation: fadeInUp 0.3s ease-out forwards;
    }
    .border-gradient-to-b {
        border-image: linear-gradient(to bottom, rgba(96,165,250,0.5), rgba(96,165,250,0.5)) 1;
    }
    [x-cloak] {
        display: none !important;
    }
</style>

<script>
    // Debug Alpine.js state
    document.addEventListener('alpine:init', () => {
        Alpine.effect(() => {
            console.log('Sidebar expanded:', Alpine.store('sidebarExpanded'));
        });
    });
</script>
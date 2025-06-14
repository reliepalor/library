<nav x-data="{ open: false, expanded: window.innerWidth > 768 }" 
    @resize.window="expanded = window.innerWidth > 768"
    class="fixed inset-y-0 left-0 z-50 bg-white dark:bg-white shadow-xl border-r dark:border-gray-300 transition-all duration-300 ease-in-out "
    :class="{'w-16': !expanded, 'w-64': expanded}">
   
   <!-- Logo -->
   <div class="flex items-center justify-center h-16 border-b dark:border-gray-300">
       <a href="{{ route('dashboard') }}" class="flex items-center justify-center">
        <img src="/images/library.png" alt="" width="30" height="30">           
        <span x-show="expanded" class="ml-2 text-gray-800 font-semibold transition-opacity duration-300" x-transition:enter="ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">Library Admin</span>
       </a>
   </div>

   <!-- Main Navigation -->
   <div class="mt-4 grid rounded-lg p-1">
       <!-- Dashboard Link -->
       <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" class="flex items-center px-4 py-3">
           <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="gray">
               <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
           </svg>
           <span x-show="expanded" class="ml-3 transition-opacity duration-300 text-gray-800" x-transition:enter="ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" >Dashboard</span>
       </x-nav-link>
        
       <!-- Books Link -->
       <x-nav-link :href="route('admin.books.index')" :active="request()->routeIs('admin.books.index')" class="flex items-center px-4 py-3">
           <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="gray">
               <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
           </svg>
           <span x-show="expanded" class="ml-3 transition-opacity text-gray-800 duration-300" x-transition:enter="ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">Books</span>
       </x-nav-link>


   </div>

   <!-- User Profile Section (Bottom) -->
   <div class="absolute bottom-10 left-0 right-0 border-t dark:border-gray-300 p-4 bg-white dark:bg-white">
       <x-dropdown align="right" width="48">
           <x-slot name="trigger">
               <button class="flex items-center w-full text-left focus:outline-none">
                   <div class="flex-shrink-0">
                       <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 rounded-full bg-gray-200 p-1 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                           <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                       </svg>
                   </div>
                   <div x-show="expanded" class="ml-3 overflow-hidden transition-opacity duration-300" x-transition:enter="ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                       <div class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</div>
                       <div class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</div>
                   </div>
                   <div x-show="expanded" class="ml-auto">
                       <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                           <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                       </svg>
                   </div>
               </button>
           </x-slot>

           <x-slot name="content">
               <x-dropdown-link :href="route('profile.edit')">
                   {{ __('Profile') }}
               </x-dropdown-link>
               <form method="POST" action="{{ route('logout') }}">
                   @csrf
                   <x-dropdown-link :href="route('logout')"
                           onclick="event.preventDefault();
                                       this.closest('form').submit();">
                       {{ __('Log Out') }}
                   </x-dropdown-link>
               </form>
           </x-slot>
       </x-dropdown>
   </div>

   <!-- Toggle Button -->
   <button @click="expanded = !expanded" 
           class="absolute -right-3 top-6 bg-white dark:bg-white border dark:border-gray-300 rounded-full p-1 shadow-md hover:bg-gray-100 transition-all duration-300">
       <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" viewBox="0 0 20 20" fill="currentColor">
           <path x-show="!expanded" fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
           <path x-show="expanded" fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
       </svg>
   </button>
</nav>

<!-- Main Content Area (Add padding to account for sidebar) -->
<main :class="{'ml-16': !expanded, 'ml-64': expanded}" class="transition-all duration-300 ease-in-out pt-4">
   <!-- Your page content goes here -->
</main>

<style>
   /* Smooth transitions for the sidebar */
   [x-cloak] { display: none !important; }
   
   /* Custom nav link styling for the sidebar */
   .nav-link {
       display: flex;
       align-items: center;
       padding: 0.75rem 1rem;
       color: #4b5563;
       transition: all 0.3s ease;
   }
   
   .nav-link:hover {
       background-color: #f3f4f6;
   }
   
   .nav-link.active {
       background-color: #e5e7eb;
       color: #111827;
       border-left: 3px solid #3b82f6;
   }
</style>
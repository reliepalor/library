<nav x-data="{ sidebarOpen: true }" class="fixed mt-2 shadow-xl border z-50 bg-white dark:bg-white rounded-lg w-64 dark:border-gray-300 h-screen">
    <div class="flex flex-col h-full">
        <div class="flex items-center justify-center h-16 border-b border-gray-200">
            <a href="{{ route('admin.auth.dashboard') }}">
                <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-800" />
            </a>
        </div>
        <nav class="flex-1 px-2 py-4 space-y-2 text-sm overflow-y-auto">
            <a href="{{ route('admin.auth.dashboard') }}" class="flex items-center p-2 rounded-lg hover:bg-indigo-50 text-gray-800">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="ml-3">Dashboard</span>
            </a>
            <a href="{{ route('admin.books.index') }}" class="flex items-center p-2 rounded-lg hover:bg-indigo-50 text-gray-800">
                <span class="material-symbols-outlined">menu_book</span>
                <span class="ml-3">Books</span>
            </a>
            <a href="{{ route('admin.students.index') }}" class="flex items-center p-2 rounded-lg hover:bg-indigo-50 text-gray-800">
                <span class="material-symbols-outlined">group</span>
                <span class="ml-3">Students</span>
            </a>
            <a href="{{ route('admin.logout') }}" class="flex items-center p-2 rounded-lg hover:bg-red-50 text-red-600">
                <span class="material-symbols-outlined">logout</span>
                <span class="ml-3">Logout</span>
            </a>
        </nav>
        <div class="p-4 border-t border-gray-200">
            <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
            <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
        </div>
    </div>
</nav>

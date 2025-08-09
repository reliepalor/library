<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin | Borrow Requests</title>
    <link rel="icon" type="image/x-icon" href="/favicon/Library.png">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        .nav-link { display: flex; align-items: center; padding: 0.75rem 1rem; color: #4b5563; transition: all 0.3s ease; }
        .nav-link:hover { background-color: #f3f4f6; }
        .nav-link.active { background-color: #e5e7eb; color: #111827; border-left: 3px solid #3b82f6; }
        .content-area { transition: margin-left 0.3s ease; }
    </style>
</head>
<body class="font-sans antialiased" x-data="{ sidebarExpanded: window.innerWidth > 768 }" @resize.window="sidebarExpanded = window.innerWidth > 768">
    <div class="min-h-screen bg-gray-100 flex">
        <x-admin-nav-bar/>

        <!-- Content Area -->
        <div class="content-area flex-1 ml-16" :class="{'ml-16': !sidebarExpanded, 'ml-64': sidebarExpanded}">
            <main class="max-w-full mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="bg-white shadow-sm rounded-lg mb-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                        <div class="flex justify-between items-center">
                            <h1 class="text-2xl font-semibold text-gray-900">Book Borrowing Management</h1>
                            <div class="flex space-x-4">
                                <button id="requestsBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                    Borrow Requests
                                </button>
                                <button id="borrowedBtn" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200">
                                    Borrowed Books
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="max-w-7xl mx-auto">
                    <!-- Borrow Requests Section -->
                    <div id="requestsSection" class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-800">Pending Borrow Requests</h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested At</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($requests as $request)
                                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    @if($request->student)
                                                        {{ $request->student->fname }} {{ $request->student->lname }}
                                                    @else
                                                        <span class="text-red-500">Student not found</span>
                                                    @endif
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    @if($request->student)
                                                        {{ $request->student->student_id }}
                                                    @else
                                                        <span class="text-red-500">ID: {{ $request->student_id }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $request->book->title }}</div>
                                                <div class="text-sm text-gray-500">{{ $request->book->book_code }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $request->created_at->format('M d, Y h:i A') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-3">
                                                    <form action="{{ route('admin.borrow.requests.approve', $request->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-green-600 hover:text-green-900">Approve</button>
                                                    </form>
                                                    <button onclick="showRejectModal({{ $request->id }})" class="text-red-600 hover:text-red-900">Reject</button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">No pending borrow requests</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Borrowed Books Section -->
                    <div id="borrowedSection" class="bg-white rounded-xl shadow-sm overflow-hidden hidden mt-6">
                        <div class="p-6 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-800">Borrowed Books</h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Borrowed At</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($borrowedBooks as $book)
                                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if($book->student)
                                                    {{ $book->student->fname }} {{ $book->student->lname }}
                                                    <div class="text-xs text-gray-400">{{ $book->student->student_id }}</div>
                                                @else
                                                    <span class="text-red-500">Unknown Student</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $book->book->name ?? 'Unknown Book' }} ({{ $book->book_id }})
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ ucfirst($book->status) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $book->created_at->format('M d, Y h:i A') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">No borrowed books</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden" aria-modal="true">
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                    <form id="rejectForm" method="POST">
                        @csrf
                        <div>
                            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Reject Borrow Request</h3>
                            <div class="mt-2">
                                <label for="rejection_reason" class="block text-sm font-medium text-gray-700">Reason for Rejection</label>
                                <textarea id="rejection_reason" name="rejection_reason" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required></textarea>
                            </div>
                        </div>
                        <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                            <button type="submit" class="inline-flex w-full justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:col-start-2 sm:text-sm">Reject</button>
                            <button type="button" onclick="hideRejectModal()" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:col-start-1 sm:mt-0 sm:text-sm">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle between sections
        const requestsBtn = document.getElementById('requestsBtn');
        const borrowedBtn = document.getElementById('borrowedBtn');
        const requestsSection = document.getElementById('requestsSection');
        const borrowedSection = document.getElementById('borrowedSection');

        requestsBtn.addEventListener('click', () => {
            requestsSection.classList.remove('hidden');
            borrowedSection.classList.add('hidden');
            requestsBtn.classList.remove('bg-gray-200', 'text-gray-700');
            requestsBtn.classList.add('bg-blue-600', 'text-white');
            borrowedBtn.classList.remove('bg-blue-600', 'text-white');
            borrowedBtn.classList.add('bg-gray-200', 'text-gray-700');
        });

        borrowedBtn.addEventListener('click', () => {
            requestsSection.classList.add('hidden');
            borrowedSection.classList.remove('hidden');
            borrowedBtn.classList.remove('bg-gray-200', 'text-gray-700');
            borrowedBtn.classList.add('bg-blue-600', 'text-white');
            requestsBtn.classList.remove('bg-blue-600', 'text-white');
            requestsBtn.classList.add('bg-gray-200', 'text-gray-700');
        });

        // Reject Modal Functions
        function showRejectModal(requestId) {
            const modal = document.getElementById('rejectModal');
            const form = document.getElementById('rejectForm');
            form.action = `/admin/borrow/requests/${requestId}/reject`;
            modal.classList.remove('hidden');
        }

        function hideRejectModal() {
            const modal = document.getElementById('rejectModal');
            modal.classList.add('hidden');
        }
    </script>
</body>
</html> 
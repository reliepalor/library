<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Admin | Dashboard</title>
        <link rel="icon" type="image/x-icon" href="/favicon/Library.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Chart.js (included for consistency, though not used in this page) -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <!-- Vite Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

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
            
            /* Ensure smooth transition for content area */
            .content-area {
                transition: margin-left 0.3s ease;
            }

            /* Animation classes */
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
    <body class="font-sans antialiased" x-data="{ sidebarExpanded: window.innerWidth > 768 }" @resize.window="sidebarExpanded = window.innerWidth > 768">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-100 flex">
        <x-admin-nav-bar />

            <!-- Content Area -->
            <div class="content-area flex-1" :class="{'ml-16': !sidebarExpanded, 'ml-64': sidebarExpanded}">
               

                <!-- Page Content -->
                <main class="max-w-full mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <!-- Welcome Section -->
                    <div class="mb-8">
                        <h1 class="text-2xl font-bold text-gray-800">Welcome back, {{ Auth::user()->name }}!</h1>
                        <p class="text-gray-600">Here's what's happening in your library today.</p>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <!-- Total Books Card -->
                        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Total Books</p>
                                    <p class="text-2xl font-semibold text-gray-800">{{ $totalBooks }}</p>
                                </div>
                                <div class="p-3 bg-blue-50 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('admin.books.index') }}" class="text-sm text-blue-600 hover:text-blue-800">View all books →</a>
                            </div>
                        </div>

                        <!-- Total Students Card -->
                        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Total Students</p>
                                    <p class="text-2xl font-semibold text-gray-800">{{ $totalStudents }}</p>
                                </div>
                                <div class="p-3 bg-green-50 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('admin.students.index') }}" class="text-sm text-green-600 hover:text-green-800">View all students →</a>
                            </div>
                        </div>

                        <!-- Active Borrows Card -->
                        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Active Borrows</p>
                                    <p class="text-2xl font-semibold text-gray-800">{{ $activeBorrows }}</p>
                                </div>
                                <div class="p-3 bg-yellow-50 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('admin.borrow.requests') }}" class="text-sm text-yellow-600 hover:text-yellow-800">View borrow requests →</a>
                            </div>
                        </div>

                        <!-- Today's Attendance Card -->
                        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Today's Attendance</p>
                                    <p class="text-2xl font-semibold text-gray-800">{{ $todayAttendance }}</p>
                                </div>
                                <div class="p-3 bg-purple-50 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('admin.attendance.index') }}" class="text-sm text-purple-600 hover:text-purple-800">View attendance →</a>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- Recent Borrow Requests -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h2 class="text-xl font-semibold mb-4">Recent Borrow Requests</h2>
                            <div class="w-full">
                                <table class="w-full table-fixed">
                                    <thead>
                                        <tr class="bg-gray-50">
                                            <th class="w-1/3 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                            <th class="w-1/3 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                                            <th class="w-1/3 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($recentBorrows as $borrow)
                                        <tr>
                                            <td class="px-4 py-3">
                                                <div class="text-sm font-medium text-gray-900 truncate">
                                                    @if($borrow->student)
                                                        {{ $borrow->student->fname }} {{ $borrow->student->lname }}
                                                    @else
                                                        <span class="text-red-500">No student</span>
                                                    @endif
                                                </div>
                                                <div class="text-sm text-gray-500 truncate">
                                                    {{ $borrow->student->student_id ?? 'N/A' }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="text-sm text-gray-900 truncate">{{ $borrow->book->name }}</div>
                                                <div class="text-sm text-gray-500 truncate">{{ $borrow->book->book_id }}</div>
                                            </td>
                                            <td class="px-4 py-3">
                                                @if($borrow->status === 'pending')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        Pending
                                                    </span>
                                                @elseif($borrow->status === 'approved')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Approved
                                                    </span>
                                                @elseif($borrow->status === 'returned')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        Returned
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Today's Attendance List -->
                        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Today's Attendance</h3>
                            <div class="space-y-4">
                                @forelse($todayAttendanceRecords as $attendance)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $attendance->student->name }}</p>
                                            <p class="text-sm text-gray-600">{{ $attendance->created_at->format('h:i A') }}</p>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                            Present
                                        </span>
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-center py-4">No attendance records for today</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Overdue Books Section  -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6 fade-in">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-6">
                                <div>
                                    <h2 class="text-xl font-semibold text-gray-800 mb-2">Overdue Books</h2>
                                    <p class="text-sm text-gray-600">Send reminders to students with overdue books</p>
                                </div>
                                <form id="sendRemindersForm" action="{{ route('admin.overdue.books.send-reminders') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                        class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 flex items-center space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                        </svg>
                                        <span>Send Reminders</span>
                                    </button>
                                </form>
                            </div>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Borrowed Date</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days Overdue</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200" id="overdueBooksList">
                                        <tr>
                                            <td colspan="5" class="px-6 py-8 text-center">
                                                <div class="flex flex-col items-center justify-center space-y-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    <h3 class="text-sm font-medium text-gray-900">No Overdue Books Yet</h3>
                                                    <p class="text-sm text-gray-500">Click "Send Reminders" to notify students with overdue books.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>

        @push('scripts')
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('overdueBooksList');
            
            function loadOverdueBooks() {
                fetch('{{ route("admin.overdue.books") }}')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(books => {
                        console.log('Received books:', books); // Debug log
                        
                        if (!books || books.length === 0) {
                            container.innerHTML = `
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center">
                                        <div class="flex flex-col items-center justify-center space-y-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <h3 class="text-sm font-medium text-gray-900">No Overdue Books Yet</h3>
                                            <p class="text-sm text-gray-500">Click "Send Reminders" to notify students with overdue books.</p>
                                        </div>
                                    </td>
                                </tr>`;
                            return;
                        }
                        
                        const html = books.map(book => {
                            const studentName = `${book.student.fname} ${book.student.lname}`;
                            const studentId = book.student.student_id;
                            const bookName = book.book.name;
                            const bookId = book.book.book_id;
                            const borrowedDate = new Date(book.created_at).toLocaleDateString();
                            const daysOverdue = book.days_overdue;
                            const emailStatus = book.email_sent ? 'Sent' : 'Pending';
                            const emailStatusClass = book.email_sent ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';

                            return `
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">${studentName}</div>
                                                <div class="text-sm text-gray-500">${studentId}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">${bookName}</div>
                                        <div class="text-sm text-gray-500">${bookId}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">${borrowedDate}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            ${daysOverdue} days
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${emailStatusClass}">
                                            ${emailStatus}
                                        </span>
                                    </td>
                                </tr>
                            `;
                        }).join('');
                        
                        container.innerHTML = html;
                    })
                    .catch(error => {
                        console.error('Error loading overdue books:', error);
                        container.innerHTML = `
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <h3 class="text-sm font-medium text-gray-900">Error loading overdue books</h3>
                                        <p class="text-sm text-gray-500">Please try again later.</p>
                                    </div>
                                </td>
                            </tr>`;
                    });
            }

            // Load overdue books when the page loads
            loadOverdueBooks();

            const sendRemindersForm = document.getElementById('sendRemindersForm');
            if (sendRemindersForm) {
                sendRemindersForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    try {
                        const response = await fetch('{{ route("admin.overdue.books.send-reminders") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken,
                            },
                            body: JSON.stringify({ _token: csrfToken })
                        });
                        const data = await response.json();
                        if (response.ok) {
                            // Show success message
                            showToast(data.message || 'Reminders sent successfully!', 'success');
                            
                            // Show list of students who received emails
                            if (data.sent_emails && data.sent_emails.length > 0) {
                                const emailList = data.sent_emails.map(email => 
                                    `<div class="text-sm text-red-600 mb-1">
                                        • ${email.name} (${email.student_id}) - ${email.college} - Book: ${email.book}
                                    </div>`
                                ).join('');
                                
                                showToast(`
                                    <div class="text-left">
                                        <div class="font-semibold mb-2">Emails sent to:</div>
                                        ${emailList}
                                    </div>
                                `, 'success', 10000); // Show for 10 seconds
                            }
                            
                            // Reload the overdue books list
                            loadOverdueBooks();
                        } else {
                            showToast(data.message || 'Failed to send reminders.', 'error');
                        }
                    } catch (error) {
                        console.error('Error sending reminders:', error);
                        showToast('An error occurred while sending reminders.', 'error');
                    }
                });
            }
        });

        function updateBorrowStatus(selectElement) {
            const borrowId = selectElement.dataset.borrowId;
            const newStatus = selectElement.value;
            
            fetch(`/admin/borrow-requests/${borrowId}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    const toast = document.createElement('div');
                    toast.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg';
                    toast.textContent = 'Status updated successfully';
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), 3000);
                } else {
                    throw new Error(data.message || 'Failed to update status');
                }
            })
            .catch(error => {
                // Show error message
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg';
                toast.textContent = error.message || 'Failed to update status';
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 3000);
                
                // Reset select to previous value
                selectElement.value = selectElement.dataset.previousValue;
            });
            
            // Store current value for potential reset
            selectElement.dataset.previousValue = selectElement.value;
        }

        // Initialize previous values when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.status-select').forEach(select => {
                select.dataset.previousValue = select.value;
            });
        });
        </script>
        @endpush
    </body>
</html>
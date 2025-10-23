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

        /* Toast progress bar animation */
        #toastProgressBar {
            transition: width 3s linear;
            transform-origin: left;
        }
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

    <!-- Toast Notification -->
    <div id="toast" class="fixed top-4 right-4 z-[60] hidden">
        <div id="toastInner" class="flex items-center gap-3 px-4 py-3 rounded shadow-lg bg-green-600 text-white min-w-[250px] relative overflow-hidden">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span id="toastMessage" class="flex-1">Success</span>
            <button onclick="hideToast()" class="ml-2 text-white hover:text-gray-200 flex-shrink-0">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <!-- Progress Bar -->
            <div id="toastProgress" class="absolute bottom-0 left-0 h-1 bg-white bg-opacity-30 w-full">
                <div id="toastProgressBar" class="h-full bg-white w-full"></div>
            </div>
        </div>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Borrower</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested At</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($requests as $request)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            @php
                                                if ($request->user_type === 'student' && $request->student) {
                                                    echo $request->student->fname . ' ' . $request->student->lname;
                                                } elseif (in_array($request->user_type, ['teacher', 'teacher_visitor'])) {
                                                    // Try to find by email first (new format), then by ID (old format)
                                                    $tv = \App\Models\TeacherVisitor::where('email', $request->student_id)->first();
                                                    if (!$tv && is_numeric($request->student_id)) {
                                                        $tv = \App\Models\TeacherVisitor::find($request->student_id);
                                                    }
                                                    echo $tv ? $tv->fname . ' ' . $tv->lname : '<span class="text-red-500">Borrower not found</span>';
                                                } else {
                                                    echo '<span class="text-red-500">Borrower not found</span>';
                                                }
                                            @endphp
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            @php
                                                if ($request->user_type === 'student' && $request->student) {
                                                    echo $request->student->student_id;
                                                } elseif (in_array($request->user_type, ['teacher', 'teacher_visitor'])) {
                                                    // Try to find by email first (new format), then by ID (old format)
                                                    $tv = \App\Models\TeacherVisitor::where('email', $request->student_id)->first();
                                                    if (!$tv && is_numeric($request->student_id)) {
                                                        $tv = \App\Models\TeacherVisitor::find($request->student_id);
                                                    }
                                                    echo $tv ? $tv->email : '<span class="text-red-500">ID: ' . $request->student_id . '</span>';
                                                } else {
                                                    echo '<span class="text-red-500">ID: ' . $request->student_id . '</span>';
                                                }
                                            @endphp
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-1">
                                    @if($request->user_type === 'student')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            <svg class="mr-1.5 h-2 w-2 text-blue-400" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            Student
                                        </span>
                                    @elseif(in_array($request->user_type, ['teacher', 'teacher_visitor']))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                            <svg class="mr-1.5 h-2 w-2 text-purple-400" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            Teacher/Visitor
                                        </span>
                                    @endif
                                </div>
                                @php
                                    $reservation = null;
                                    if ($request->user_type === 'student') {
                                        $reservation = \App\Models\Reservation::where('book_id', $request->book_id)
                                            ->where('student_id', $request->student_id)
                                            ->whereNull('teacher_visitor_email')
                                            ->whereIn('status', ['active', 'cancelled'])
                                            ->latest('reserved_at')
                                            ->first();
                                    } elseif (in_array($request->user_type, ['teacher', 'teacher_visitor'])) {
                                        // For teacher/visitor, check both email and ID formats
                                        $reservation = \App\Models\Reservation::where('book_id', $request->book_id)
                                            ->where(function($q) use ($request) {
                                                $q->where('teacher_visitor_email', $request->student_id);
                                                if (is_numeric($request->student_id)) {
                                                    $tv = \App\Models\TeacherVisitor::find($request->student_id);
                                                    if ($tv) {
                                                        $q->orWhere('teacher_visitor_email', $tv->email);
                                                    }
                                                }
                                            })
                                            ->whereIn('status', ['active', 'cancelled'])
                                            ->latest('reserved_at')
                                            ->first();
                                    }
                                @endphp
                                @if($reservation)
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="mr-1.5 h-2 w-2 text-yellow-400" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            Reserved at {{ $reservation->reserved_at->setTimezone('Asia/Manila')->format('M j, Y h:i A') }}
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $request->book->name }}</div>
                                <div class="text-sm text-gray-500">{{ $request->book->book_code }}</div>
                                @php
                                    $reservation2 = null;
                                    if ($request->user_type === 'student') {
                                        $reservation2 = \App\Models\Reservation::where('book_id', $request->book_id)
                                            ->where('student_id', $request->student_id)
                                            ->whereNull('teacher_visitor_email')
                                            ->where('status', 'active')
                                            ->latest('reserved_at')
                                            ->first();
                                    } elseif (in_array($request->user_type, ['teacher', 'teacher_visitor'])) {
                                        // For teacher/visitor, check both email and ID formats
                                        $reservation2 = \App\Models\Reservation::where('book_id', $request->book_id)
                                            ->where(function($q) use ($request) {
                                                $q->where('teacher_visitor_email', $request->student_id);
                                                if (is_numeric($request->student_id)) {
                                                    $tv = \App\Models\TeacherVisitor::find($request->student_id);
                                                    if ($tv) {
                                                        $q->orWhere('teacher_visitor_email', $tv->email);
                                                    }
                                                }
                                            })
                                            ->where('status', 'active')
                                            ->latest('reserved_at')
                                            ->first();
                                    }
                                @endphp
                                @if($reservation2)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 mt-1">
                                        <svg class="mr-1.5 h-2 w-2 text-yellow-400" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        Reserved at {{ $reservation2->reserved_at->setTimezone('Asia/Manila')->format('M j, Y h:i A') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $request->created_at->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            @if($book->user_type === 'student' && $book->student)
                                                {{ $book->student->fname }} {{ $book->student->lname }}
                                            @elseif(in_array($book->user_type, ['teacher', 'teacher_visitor']))
                                                @php
                                                    // Try to find by email first (new format), then by ID (old format)
                                                    $tvb = \App\Models\TeacherVisitor::where('email', $book->student_id)->first();
                                                    if (!$tvb && is_numeric($book->student_id)) {
                                                        $tvb = \App\Models\TeacherVisitor::find($book->student_id);
                                                    }
                                                @endphp
                                                {!! $tvb ? $tvb->fname . ' ' . $tvb->lname : '<span class="text-red-500">Unknown Borrower</span>' !!}
                                            @else
                                                <span class="text-red-500">Unknown Borrower</span>
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            @if($book->user_type === 'student' && $book->student)
                                                {{ $book->student->student_id }}
                                            @elseif(in_array($book->user_type, ['teacher', 'teacher_visitor']))
                                                @php
                                                    // Try to find by email first (new format), then by ID (old format)
                                                    $tvb = \App\Models\TeacherVisitor::where('email', $book->student_id)->first();
                                                    if (!$tvb && is_numeric($book->student_id)) {
                                                        $tvb = \App\Models\TeacherVisitor::find($book->student_id);
                                                    }
                                                @endphp
                                                {!! $tvb ? $tvb->email : '<span class="text-red-500">ID: ' . $book->student_id . '</span>' !!}
                                            @else
                                                <span class="text-red-500">ID: {{ $book->student_id }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-1">
                                    @if($book->user_type === 'student')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            <svg class="mr-1.5 h-2 w-2 text-blue-400" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            Student
                                        </span>
                                    @elseif(in_array($book->user_type, ['teacher', 'teacher_visitor']))
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                            <svg class="mr-1.5 h-2 w-2 text-purple-400" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            Teacher/Visitor
                                        </span>
                                    @endif
                                </div>
                                @php
                                    $borrowedReservation = null;
                                    if ($book->user_type === 'student') {
                                        $borrowedReservation = \App\Models\Reservation::where('book_id', $book->book_id)
                                            ->where('student_id', $book->student_id)
                                            ->whereNull('teacher_visitor_email')
                                            ->latest('reserved_at')
                                            ->first();
                                    } elseif (in_array($book->user_type, ['teacher', 'teacher_visitor'])) {
                                        // For teacher/visitor, check both email and ID formats
                                        $borrowedReservation = \App\Models\Reservation::where('book_id', $book->book_id)
                                            ->where(function($q) use ($book) {
                                                $q->where('teacher_visitor_email', $book->student_id);
                                                if (is_numeric($book->student_id)) {
                                                    $tv = \App\Models\TeacherVisitor::find($book->student_id);
                                                    if ($tv) {
                                                        $q->orWhere('teacher_visitor_email', $tv->email);
                                                    }
                                                }
                                            })
                                            ->latest('reserved_at')
                                            ->first();
                                    }
                                @endphp
                                @if($borrowedReservation)
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="mr-1.5 h-2 w-2 text-yellow-400" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            Reserved at {{ $borrowedReservation->reserved_at->setTimezone('Asia/Manila')->format('M j, Y h:i A') }}
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <a href="#" onclick="showBookImage('{{ $book->book->image1 ?? '' }}', '{{ $book->book->name ?? 'Unknown Book' }}')" 
                                   class="text-blue-600 hover:underline">
                                    {{ $book->book->name ?? 'Unknown Book' }} ({{ $book->book_id }})
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($book->status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ ucfirst($book->status) }}
                                    </span>
                                    <button onclick="markAsReturned({{ $book->id }})" 
                                            class="ml-2 text-xs text-blue-600 hover:text-blue-800 underline">
                                        Mark as Returned
                                    </button>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $book->status === 'returned' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($book->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $book->created_at->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}
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

    <!-- Book Image Modal -->
    <div id="bookImageModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-hidden shadow-xl">
                <div class="flex justify-between items-center px-6 py-4 border-b">
                    <h3 id="bookImageTitle" class="text-lg font-medium text-gray-900"></h3>
                    <button onclick="document.getElementById('bookImageModal').classList.add('hidden')" 
                            class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 flex justify-center">
                    <img id="bookImage" src="" alt="Book Cover" class="max-h-[70vh] max-w-full object-contain">
                </div>
            </div>
        </div>
    </div>

    <!-- Return Confirmation Modal -->
    <div id="returnConfirmationModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-md w-full p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Confirm Return</h3>
                <p class="text-gray-600 mb-6">Are you sure you want to mark this book as returned?</p>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="hideReturnConfirmation()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                        Cancel
                    </button>
                    <button type="button" id="confirmReturnBtn" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                        Mark as Returned
                    </button>
                </div>
            </div>
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
                            <button type="submit" id="rejectSubmitBtn" class="inline-flex w-full justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:col-start-2 sm:text-sm">
                                <span id="rejectBtnText">Reject</span>
                                <svg id="rejectSpinner" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </button>
                            <button type="button" onclick="hideRejectModal()" class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:col-start-1 sm:mt-0 sm:text-sm">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Enhanced toast helper with progress bar
        let toastTimeout;
        let progressInterval;

        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastInner = document.getElementById('toastInner');
            const toastMessage = document.getElementById('toastMessage');
            const progressBar = document.getElementById('toastProgressBar');

            // Clear any existing timeouts/intervals
            if (toastTimeout) clearTimeout(toastTimeout);
            if (progressInterval) clearInterval(progressInterval);

            toastMessage.textContent = message || '';

            // Set style by type
            if (type === 'error') {
                toastInner.classList.remove('bg-green-600');
                toastInner.classList.add('bg-red-600');
            } else {
                toastInner.classList.remove('bg-red-600');
                toastInner.classList.add('bg-green-600');
            }

            toast.classList.remove('hidden');

            // Start progress bar animation
            progressBar.style.width = '100%';
            progressBar.style.transition = 'width 0s linear';

            // Delay before starting progress bar shrink
            setTimeout(() => {
                progressBar.style.width = '0%';
                progressBar.style.transition = 'width 3s linear';
            }, 100);

            // Auto-hide after 3.2 seconds
            toastTimeout = setTimeout(() => {
                hideToast();
            }, 3200);
        }

        function hideToast() {
            const toast = document.getElementById('toast');
            const progressBar = document.getElementById('toastProgressBar');

            // Clear timeouts/intervals
            if (toastTimeout) clearTimeout(toastTimeout);
            if (progressInterval) clearInterval(progressInterval);

            // Reset progress bar
            progressBar.style.width = '0%';
            progressBar.style.transition = 'none';

            toast.classList.add('hidden');
        }
        // Return confirmation modal functions
        let currentBorrowId = null;

        function showReturnConfirmation(borrowId) {
            currentBorrowId = borrowId;
            document.getElementById('returnConfirmationModal').classList.remove('hidden');
        }

        function hideReturnConfirmation() {
            document.getElementById('returnConfirmationModal').classList.add('hidden');
            currentBorrowId = null;
        }

        // Mark book as returned - shows confirmation modal
        function markAsReturned(borrowId) {
            showReturnConfirmation(borrowId);
        }

        // Handle confirm return button click
        document.getElementById('confirmReturnBtn').addEventListener('click', function() {
            if (!currentBorrowId) return;
            
            // Show loading state
            const button = this;
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...';
            
            // Use the correct route that matches your web.php
            const returnUrl = `/admin/borrow/requests/${currentBorrowId}/return`;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            
            fetch(returnUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    hideReturnConfirmation();
                    showToast('Book marked as returned', 'success');
                    setTimeout(() => window.location.reload(), 3200); // Wait a bit longer than toast duration
                } else {
                    showToast('Failed to update status: ' + (data.message || 'Unknown error'), 'error');
                    button.disabled = false;
                    button.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the status');
                button.disabled = false;
                button.innerHTML = originalText;
            })
            .finally(() => {
                // no-op
            });
        });

        // Close modal when clicking outside
        document.getElementById('returnConfirmationModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideReturnConfirmation();
            }
        });

        // Show book image in modal
        function showBookImage(imageUrl, bookTitle) {
            if (!imageUrl) {
                alert('No image available for this book');
                return;
            }
            
            const modal = document.getElementById('bookImageModal');
            const img = document.getElementById('bookImage');
            const title = document.getElementById('bookImageTitle');
            
            img.src = '/storage/' + imageUrl;
            title.textContent = bookTitle || 'Book Cover';
            modal.classList.remove('hidden');
            
            // Close modal when clicking outside the image
            modal.onclick = function(e) {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            };
        }
        
        /* Legacy confirm-based return function removed. Modal-based return flow is defined above. */
        
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

        // Handle approve button clicks with real-time updates
        document.addEventListener('submit', function(e) {
            if (e.target.matches('form[action*="approve"]')) {
                e.preventDefault();
                const form = e.target;
                const originalText = form.querySelector('button').innerHTML;

                // Show loading state
                form.querySelector('button').innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Approving...';
                form.querySelector('button').disabled = true;

                const formData = new FormData(form);
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    showToast('Borrow request approved and attendance updated!', 'success');
                    // Refresh the page to show updated data
                    setTimeout(() => window.location.reload(), 1500);
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('An error occurred while approving the request', 'error');
                    // Reset button state
                    form.querySelector('button').innerHTML = originalText;
                    form.querySelector('button').disabled = false;
                });
            }
        });

        // Handle reject form submission
        document.getElementById('rejectForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = this;
            const submitBtn = document.getElementById('rejectSubmitBtn');
            const btnText = document.getElementById('rejectBtnText');
            const spinner = document.getElementById('rejectSpinner');
            const originalText = btnText.textContent;

            // Show spinner and disable button
            btnText.textContent = 'Sending...';
            spinner.classList.remove('hidden');
            submitBtn.disabled = true;

            const formData = new FormData(form);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    hideRejectModal();
                    showToast('Borrow request rejected and attendance updated!', 'success');
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showToast('Failed to reject request: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred while rejecting the request', 'error');
            })
            .finally(() => {
                // Reset button state
                btnText.textContent = originalText;
                spinner.classList.add('hidden');
                submitBtn.disabled = false;
            });
        });
    </script>
</body>
</html> 
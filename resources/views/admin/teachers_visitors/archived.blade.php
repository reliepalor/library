<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Admin | Archived Teachers/Visitors</title>
        <link rel="icon" type="image/x-icon" href="/favicon/Library.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Vite Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="text-2xl font-semibold">Archived Teachers/Visitors</h2>
                                <a href="{{ route('admin.teachers_visitors.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700 transition">
                                    Back to Active Teachers/Visitors
                                </a>
                            </div>

                            @if($teachersVisitors->isEmpty())
                                <div class="text-center text-gray-500 py-8">
                                    No archived teachers/visitors found.
                                </div>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">College</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Archived At</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($teachersVisitors as $teacherVisitor)
                                                <tr id="row-{{ $teacherVisitor->id }}">
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $teacherVisitor->id }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $teacherVisitor->full_name }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $teacherVisitor->department }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $teacherVisitor->role }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $teacherVisitor->archived_at->format('M d, Y H:i') }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <form action="{{ route('admin.teachers_visitors.unarchive', $teacherVisitor->id) }}" method="POST" class="inline">
                                                            @csrf
                                                            @method('POST')
                                                            <button type="submit" class="text-indigo-600 hover:text-indigo-900 mr-3" title="Restore">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                        <button onclick="showDeleteModal({{ $teacherVisitor->id }}, '{{ $teacherVisitor->full_name }}')" 
                                                                class="text-red-600 hover:text-red-900" title="Delete Permanently">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="delete-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Confirm Permanent Deletion</h3>
                    <button onclick="closeDeleteModal()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <p class="text-gray-700 mb-6">Are you sure you want to permanently delete <span id="delete-name" class="font-semibold"></span>? This action cannot be undone.</p>
                <div class="flex justify-end space-x-3">
                    <button onclick="closeDeleteModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400">
                        Cancel
                    </button>
                    <form id="delete-form" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            Delete Permanently
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <script>
            function showDeleteModal(id, name) {
                const modal = document.getElementById('delete-modal');
                const form = document.getElementById('delete-form');
                const nameSpan = document.getElementById('delete-name');
                
                // Set the form action
                form.action = `/admin/teachers_visitors/${id}/force-delete`;
                
                // Set the name in the confirmation message
                nameSpan.textContent = name;
                
                // Show the modal
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                
                // Prevent body scroll when modal is open
                document.body.style.overflow = 'hidden';
            }
            
            function closeDeleteModal() {
                const modal = document.getElementById('delete-modal');
                modal.classList.remove('flex');
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
            
            // Close modal when clicking outside
            window.onclick = function(event) {
                const modal = document.getElementById('delete-modal');
                if (event.target === modal) {
                    closeDeleteModal();
                }
            }
            
            // Handle form submission
            document.getElementById('delete-form').addEventListener('submit', function(e) {
                e.preventDefault();
                const form = this;
                const formData = new FormData(form);
                
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-HTTP-Method-Override': 'DELETE'
                    },
                    body: JSON.stringify(Object.fromEntries(formData)),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the row from the table
                        const id = form.action.split('/').pop();
                        document.getElementById(`row-${id}`).remove();
                        
                        // Show success message
                        showToast('Teacher/Visitor permanently deleted successfully', 'success');
                        
                        // Close the modal
                        closeDeleteModal();
                        
                        // If no more rows, show empty state
                        if (document.querySelectorAll('tbody tr').length === 0) {
                            const tbody = document.querySelector('tbody');
                            tbody.innerHTML = `
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No archived teachers/visitors found.
                                    </td>
                                </tr>
                            `;
                        }
                    } else {
                        throw new Error(data.message || 'Failed to delete');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast(error.message || 'An error occurred while deleting', 'error');
                });
            });
            
            function showToast(message, type = 'success') {
                // Create toast element if it doesn't exist
                let toast = document.getElementById('toast');
                if (!toast) {
                    toast = document.createElement('div');
                    toast.id = 'toast';
                    document.body.appendChild(toast);
                }
                
                // Set toast content and styles
                toast.textContent = message;
                toast.className = `fixed top-6 right-6 z-50 px-6 py-3 rounded-md shadow-lg text-white font-medium transition-all duration-300 ${
                    type === 'success' ? 'bg-green-600' : 'bg-red-600'
                }`;
                
                // Show toast
                toast.style.display = 'block';
                
                // Hide toast after 3 seconds
                setTimeout(() => {
                    toast.style.display = 'none';
                }, 3000);
            }
        </script>
    </body>
</html> 
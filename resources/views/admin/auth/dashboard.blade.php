<div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6 fade-in">
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-2">Overdue Books</h2>
                <p class="text-sm text-gray-600">Send reminders to students with overdue books</p>
            </div>
            <form id="sendRemindersForm" class="inline">
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
        
        <div id="overdueBooksList" class="space-y-4">
            <!-- Loading state -->
            <div class="text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-gray-300 border-t-red-500"></div>
                <p class="mt-2 text-sm text-gray-600">Loading overdue books...</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('overdueBooksList');
    
    // Load overdue reminder logs (students who received reminders)
    fetch('{{ route("admin.overdue.books") }}')
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(logs => {
            if (!logs || logs.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8 slide-up">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No Students with Overdue Book Reminders</h3>
                        <p class="mt-1 text-sm text-gray-500">Students who receive overdue book reminders will appear here.</p>
                    </div>`;
                return;
            }

            const html = logs.map((entry, index) => {
                const student = entry.student || {};
                const studentName = `${student.fname || ''} ${student.lname || ''}`.trim();
                const studentId = student.student_id || '';
                const booksList = (entry.books || []).map(b => `${b.name} (${b.book_id})`).join(', ');
                const totalBooks = entry.total_books || (entry.books ? entry.books.length : 0);
                const emailStatus = entry.email_sent ? 'Sent' : 'Pending';
                const emailStatusClass = entry.email_sent ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';
                const avatarUrl = student.avatar_url || '/images/default-profile.png';

                return `
                    <div class="bg-gray-50 rounded-lg p-4 slide-up" style="animation-delay: ${index * 0.05}s">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <img src="${avatarUrl}" alt="${studentName}" class="w-10 h-10 rounded-full object-cover" onerror="this.src='/images/default-profile.png'" />
                                    </div>
                                    <div>
                                        <h3 class="font-medium text-gray-900">${studentName}</h3>
                                        <p class="text-sm text-gray-600">Student ID: ${studentId}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ${emailStatusClass}">
                                    ${emailStatus}
                                </div>
                                <p class="text-xs text-gray-500 mt-2">Books: ${totalBooks}</p>
                            </div>
                        </div>
                        <div class="mt-2 text-sm text-gray-700">
                            ${booksList}
                        </div>
                    </div>
                `;
            }).join('');

            container.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading overdue books:', error);
            container.innerHTML = `
                <div class="text-center py-8 slide-up">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Error loading overdue books</h3>
                    <p class="mt-1 text-sm text-gray-500">Please try again later.</p>
                </div>`;
        });

    const sendRemindersForm = document.getElementById('sendRemindersForm');
    if (sendRemindersForm) {
        sendRemindersForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const btn = sendRemindersForm.querySelector('button[type="submit"]');
            const originalBtnHtml = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.classList.add('opacity-70', 'cursor-not-allowed');
                btn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    Sending…`;
            }
            try {
                const response = await fetch('{{ route("admin.overdue.books.send-reminders") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });
                const data = await response.json();
                if (response.ok) {
                    showToast(data.message || 'Reminders sent successfully!', 'success');
                } else {
                    showToast(data.message || 'Failed to send reminders.', 'error');
                }
            } catch (error) {
                showToast('An error occurred while sending reminders.', 'error');
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.classList.remove('opacity-70', 'cursor-not-allowed');
                    btn.innerHTML = originalBtnHtml;
                }
            }
        });
    }
});
</script>
@endpush

<style>
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
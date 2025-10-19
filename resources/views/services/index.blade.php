
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="/images/library.png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Scroll Animation Keyframes */
        .animate-on-scroll {
            transition: all 0.8s ease-out;
        }
        .animate-on-scroll.show {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }

        
    </style>
</head>
<body>
<x-header/>

<x-service-hero-section/>

<!-- QR Code Registration Modal -->
<div id="qrModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- Modal container -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Register for Library QR Code
                        </h3>

                        <!-- Toggle Buttons -->
                        <div class="flex justify-center mb-6">
                            <div class="bg-gray-100 p-1 rounded-lg">
                                <button id="studentBtn" type="button" class="px-4 py-2 text-sm font-medium rounded-md transition-all duration-300 bg-blue-600 text-white">
                                    Student
                                </button>
                                <button id="teacherVisitorBtn" type="button" class="px-4 py-2 text-sm font-medium rounded-md transition-all duration-300 text-gray-700 hover:text-gray-900">
                                    Teacher/Visitor
                                </button>
                            </div>
                        </div>

                        <div class="mt-2">
                            <p class="text-sm text-gray-500 mb-4" id="formDescription">
                                Please provide your student information to generate your personalized QR code.
                            </p>
                            
                            <!-- Student Form -->
                            <form id="studentForm" class="space-y-4 transition-all duration-300">
                                @csrf
                                <input type="hidden" name="type" value="student">
                                <div>
                                    <label for="student_id" class="block text-sm font-medium text-gray-700">Student ID</label>
                                    <input type="text" id="student_id" name="student_id" required
                                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="fname" class="block text-sm font-medium text-gray-700">First Name</label>
                                        <input type="text" id="fname" name="fname" required
                                            class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                                    </div>

                                    <div>
                                        <label for="lname" class="block text-sm font-medium text-gray-700">Last Name</label>
                                        <input type="text" id="lname" name="lname" required
                                            class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="MI" class="block text-sm font-medium text-gray-700">Middle Initial</label>
                                        <input type="text" id="MI" name="MI" required
                                            class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                                    </div>

                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                                        <input type="email" id="email" name="email" required
                                            class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="college" class="block text-sm font-medium text-gray-700">College</label>
                                        <select id="college" name="college" required
                                            class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                                            <option value="">Select College</option>
                                            <option value="CICS">CICS</option>
                                            <option value="CTED">CTED</option>
                                            <option value="CCJE">CCJE</option>
                                            <option value="CHM">CHM</option>
                                            <option value="CBEA">CBEA</option>
                                            <option value="CA">CA</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="year" class="block text-sm font-medium text-gray-700">Year Level</label>
                                        <input type="number" id="year" name="year" min="1" max="5" required
                                            class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                                    </div>
                                </div>
                            </form>

                            <!-- Teacher/Visitor Form -->
                            <form id="teacherVisitorForm" class="space-y-4 transition-all duration-300 hidden">
                                @csrf
                                <input type="hidden" name="type" value="teacher_visitor">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="tv_fname" class="block text-sm font-medium text-gray-700">First Name</label>
                                        <input type="text" id="tv_fname" name="fname" required
                                            class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                                    </div>

                                    <div>
                                        <label for="tv_lname" class="block text-sm font-medium text-gray-700">Last Name</label>
                                        <input type="text" id="tv_lname" name="lname" required
                                            class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="tv_MI" class="block text-sm font-medium text-gray-700">Middle Initial</label>
                                        <input type="text" id="tv_MI" name="MI"
                                            class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                                    </div>

                                    <div>
                                        <label for="tv_email" class="block text-sm font-medium text-gray-700">Email Address</label>
                                        <input type="email" id="tv_email" name="email" required
                                            class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="department" class="block text-sm font-medium text-gray-700">Department</label>
                                        <select id="department" name="department" required
                                            class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                                            <option value="">Select Department</option>
                                            <option value="CICS">CICS</option>
                                            <option value="CTED">CTED</option>
                                            <option value="CCJE">CCJE</option>
                                            <option value="CHM">CHM</option>
                                            <option value="CBEA">CBEA</option>
                                            <option value="CA">CA</option>
                                            <option value="Guest">Guest</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                                        <select id="role" name="role" required
                                            class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                                            <option value="">Select Role</option>
                                            <option value="Teacher">Teacher</option>
                                            <option value="Visitor">Visitor</option>
                                        </select>
                                    </div>
                                </div>
                            </form>
                                
                                <div id="formErrors" class="hidden bg-red-50 border-l-4 border-red-500 p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-red-700" id="errorMessage"></p>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="submitQrForm" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Register
                </button>
                <button type="button" id="cancelModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Loading Spinner Modal -->
<div id="loadingModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- Spinner container -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <div class="flex justify-center">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                        </div>
                        <div class="mt-4 text-center">
                            <p class="text-lg font-medium text-gray-900">Processing your request...</p>
                            <p class="text-sm text-gray-500 mt-2">Please wait while we generate your QR code</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success/Error Message Modal -->
<div id="messageModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- Modal container -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <div class="flex justify-center">
                            <div id="messageIcon" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full">
                                <!-- Icon will be inserted by JavaScript -->
                            </div>
                        </div>
                        <div class="mt-4 text-center">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="messageTitle"></h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" id="messageContent"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="closeMessageModal" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Modal functionality
    const modal = document.getElementById('qrModal');
    const registerBtn = document.getElementById('registerQrBtn');
    const cancelBtn = document.getElementById('cancelModal');
    const submitBtn = document.getElementById('submitQrForm');
    const studentForm = document.getElementById('studentForm');
    const teacherVisitorForm = document.getElementById('teacherVisitorForm');
    const formErrors = document.getElementById('formErrors');
    const errorMessage = document.getElementById('errorMessage');
    const formDescription = document.getElementById('formDescription');

    // Toggle buttons
    const studentBtn = document.getElementById('studentBtn');
    const teacherVisitorBtn = document.getElementById('teacherVisitorBtn');

    let currentForm = 'student';

    // Show modal
    registerBtn.addEventListener('click', () => {
        // Check if user is authenticated
        @auth
            modal.classList.remove('hidden');
        @else
            // Redirect to login page if not authenticated
            window.location.href = '{{ route("login") }}';
        @endauth
    });

    // Hide modal
    function hideModal() {
        modal.classList.add('hidden');
        studentForm.reset();
        teacherVisitorForm.reset();
        formErrors.classList.add('hidden');
        // Reset to student form
        switchToStudent();
    }

    // Cancel button
    cancelBtn.addEventListener('click', hideModal);

    // Toggle between forms
    studentBtn.addEventListener('click', switchToStudent);
    teacherVisitorBtn.addEventListener('click', switchToTeacherVisitor);

    function switchToStudent() {
        currentForm = 'student';
        studentBtn.classList.remove('text-gray-700', 'hover:text-gray-900');
        studentBtn.classList.add('bg-blue-600', 'text-white');
        teacherVisitorBtn.classList.remove('bg-blue-600', 'text-white');
        teacherVisitorBtn.classList.add('text-gray-700', 'hover:text-gray-900');

        studentForm.classList.remove('hidden');
        teacherVisitorForm.classList.add('hidden');
        formDescription.textContent = 'Please provide your student information to generate your personalized QR code.';
    }

    function switchToTeacherVisitor() {
        currentForm = 'teacher_visitor';
        teacherVisitorBtn.classList.remove('text-gray-700', 'hover:text-gray-900');
        teacherVisitorBtn.classList.add('bg-blue-600', 'text-white');
        studentBtn.classList.remove('bg-blue-600', 'text-white');
        studentBtn.classList.add('text-gray-700', 'hover:text-gray-900');

        teacherVisitorForm.classList.remove('hidden');
        studentForm.classList.add('hidden');
        formDescription.textContent = 'Please provide your teacher/visitor information to generate your personalized QR code.';
    }

    // Submit form
    submitBtn.addEventListener('click', function(e) {
        e.preventDefault();

        // Show loading spinner
        const loadingModal = document.getElementById('loadingModal');
        loadingModal.classList.remove('hidden');

        // Hide any previous errors
        formErrors.classList.add('hidden');

        // Get form data based on current form
        const form = currentForm === 'student' ? studentForm : teacherVisitorForm;
        const formData = new FormData(form);

        // Submit via AJAX
        fetch('{{ route("services.register-qr") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            // Hide loading spinner
            loadingModal.classList.add('hidden');

            if (data.success) {
                // Show success message in modal
                showMessageModal('Success', data.message || 'Registration successful! Your QR code has been sent to your email.', true);
                hideModal();
                form.reset();
            } else {
                // Show errors
                errorMessage.textContent = data.message || 'An error occurred during registration.';
                formErrors.classList.remove('hidden');
            }
        })
        .catch(error => {
            // Hide loading spinner
            loadingModal.classList.add('hidden');

            console.error('Error:', error);
            errorMessage.textContent = 'An error occurred during registration. Please try again.';
            formErrors.classList.remove('hidden');
        });
    });
    
    // Show message modal
    function showMessageModal(title, message, isSuccess) {
        const messageModal = document.getElementById('messageModal');
        const messageTitle = document.getElementById('messageTitle');
        const messageContent = document.getElementById('messageContent');
        const messageIcon = document.getElementById('messageIcon');
        const closeMessageModal = document.getElementById('closeMessageModal');
        
        messageTitle.textContent = title;
        messageContent.textContent = message;
        
        // Set icon based on success or error
        if (isSuccess) {
            messageIcon.innerHTML = `
                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            `;
            messageIcon.className = "mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100";
        } else {
            messageIcon.innerHTML = `
                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            `;
            messageIcon.className = "mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100";
        }
        
        messageModal.classList.remove('hidden');
        
        // Close modal when close button is clicked
        closeMessageModal.addEventListener('click', function() {
            messageModal.classList.add('hidden');
        });
    }

    // Function to convert text to title case
    function toTitleCase(str) {
        return str.toLowerCase().split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    // Apply title case to name fields on blur
    function applyTitleCase(event) {
        event.target.value = toTitleCase(event.target.value);
    }

    // Intersection Observer for Scroll Animations
document.addEventListener("DOMContentLoaded", function() {
    const elements = document.querySelectorAll(".animate-on-scroll");
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add("show");
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    elements.forEach(el => observer.observe(el));

    // Add title case functionality to name fields
    const nameFields = ['fname', 'lname', 'tv_fname', 'tv_lname'];
    nameFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('blur', applyTitleCase);
        }
    });
});

</script>
</body>
</html>
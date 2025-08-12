
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background-color: #fff;
            background-image:
                linear-gradient(45deg, #ccc 25%, transparent 25%),
                linear-gradient(-45deg, #ccc 25%, transparent 25%),
                linear-gradient(45deg, transparent 75%, #ccc 75%),
                linear-gradient(-45deg, transparent 75%, #ccc 75%);
            background-size: 20px 20px;
            background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
        }

    </style>
</head>
<body>
<x-header/>
<div class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Page Header -->
        <div class="text-center mb-16 mt-8">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4 tracking-tight">Our Library Services</h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Experience modern library services designed to enhance your learning journey with cutting-edge technology and seamless user experience.
            </p>
        </div>

        <!-- Services Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12">
            <!-- QR Code Attendance -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                <div class="flex justify-center mb-6">
                    <div class="bg-blue-50 p-4 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                    </div>
                </div>
                <h2 class="text-2xl font-semibold text-gray-900 text-center mb-3">QR Code Attendance</h2>
                <p class="text-gray-600 text-center mb-6">
                    Streamline your library visits with our QR code attendance system. Simply scan the code upon entry for quick and efficient check-in.
                </p>
                <ul class="space-y-2 mb-6">
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-gray-600">Instant check-in process</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-gray-600">Accurate attendance records</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-gray-600">Reduced waiting times</span>
                    </li>
                </ul>
                <div class="text-center">
                    <a href="{{ route('user.attendance.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        Learn More
                        <svg class="ml-2 -mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Book Borrowing/Returning -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                <div class="flex justify-center mb-6">
                    <div class="bg-green-50 p-4 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                </div>
                <h2 class="text-2xl font-semibold text-gray-900 text-center mb-3">Book Borrowing & Returning</h2>
                <p class="text-gray-600 text-center mb-6">
                    Effortlessly borrow and return books with our intuitive system. Track your loans, due dates, and reservations in real-time.
                </p>
                <ul class="space-y-2 mb-6">
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-gray-600">Easy reservation system</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-gray-600">Real-time inventory tracking</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-gray-600">Automated due date reminders</span>
                    </li>
                </ul>
                <div class="text-center">
                    <a href="{{ route('user.books.index') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors duration-200">
                        Browse Books
                        <svg class="ml-2 -mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Real-time Email Notifications -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                <div class="flex justify-center mb-6">
                    <div class="bg-purple-50 p-4 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <h2 class="text-2xl font-semibold text-gray-900 text-center mb-3">Real-time Email Notifications</h2>
                <p class="text-gray-600 text-center mb-6">
                    Stay informed with instant email alerts about due dates, overdue books, and library announcements.
                </p>
                <ul class="space-y-2 mb-6">
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-gray-600">Due date reminders</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-gray-600">Overdue notifications</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-gray-600">Library updates</span>
                    </li>
                </ul>
                <div class="text-center">
                    <a href="{{ route('user.profile.edit') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors duration-200">
                        Manage Preferences
                        <svg class="ml-2 -mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Additional Information Section -->
        <div class="mt-20 bg-white rounded-2xl shadow-sm border border-gray-100 p-8 max-w-4xl mx-auto">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Why Choose Our Library Services?</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Our library services are designed with you in mind, combining cutting-edge technology with user-friendly interfaces to enhance your learning experience.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="flex items-start">
                    <div class="flex-shrink-0 mt-1">
                        <div class="flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Lightning Fast</h3>
                        <p class="mt-2 text-gray-600">
                            Our systems are optimized for speed, ensuring you spend less time waiting and more time learning.
                        </p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0 mt-1">
                        <div class="flex items-center justify-center h-10 w-10 rounded-full bg-green-100 text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Secure & Private</h3>
                        <p class="mt-2 text-gray-600">
                            Your data is protected with industry-standard security measures and privacy protocols.
                        </p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0 mt-1">
                        <div class="flex items-center justify-center h-10 w-10 rounded-full bg-purple-100 text-purple-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">Always Available</h3>
                        <p class="mt-2 text-gray-600">
                            Access our digital services 24/7 from any device with an internet connection.
                        </p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <div class="flex-shrink-0 mt-1">
                        <div class="flex items-center justify-center h-10 w-10 rounded-full bg-yellow-100 text-yellow-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-gray-900">User-Friendly Design</h3>
                        <p class="mt-2 text-gray-600">
                            Intuitive interfaces designed to make your library experience as smooth as possible.
                        </p>
                    </div>
                </div>
            </div>
        </div>
         
        <!-- QR Code Section -->
        <div class="mt-20 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl shadow-sm border border-gray-100 p-8 max-w-4xl mx-auto">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Get Your Library QR Code</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Register now to get your personalized QR code for quick library access, book borrowing, and attendance tracking.
                </p>
            </div>
            
            <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="flex-1">
                    <img src="{{ asset('images/library.png') }}" alt="Library QR Code Example" class="mx-auto rounded-xl shadow-lg border border-gray-200">
                </div>
                <div class="flex-1 text-center md:text-left">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Benefits of Having a Library QR Code</h3>
                    <ul class="space-y-3 text-gray-600 mb-6">
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Quick check-in at library entrance</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Easy book borrowing and returning</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Attendance tracking for study sessions</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Personalized library experience</span>
                        </li>
                    </ul>
                    <button id="registerQrBtn" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white text-base font-medium rounded-lg shadow-md hover:bg-blue-700 transition-all duration-200 transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Register for QR Code
                        <svg class="ml-2 -mr-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
         
        <!-- Call to Action -->
        <div class="mt-16 text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Ready to Experience Our Services?</h2>
            <p class="text-gray-600 max-w-2xl mx-auto mb-8">
                Join thousands of students who are already benefiting from our modern library services.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                @guest
                    <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white text-base font-medium rounded-lg shadow-md hover:bg-blue-700 transition-all duration-200 transform hover:-translate-y-0.5">
                        Create Account
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 bg-white text-gray-900 text-base font-medium rounded-lg shadow-md hover:bg-gray-50 transition-all duration-200 border border-gray-200">
                        Sign In
                    </a>
                @endguest
                @auth
                    <a href="{{ route('user.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white text-base font-medium rounded-lg shadow-md hover:bg-blue-700 transition-all duration-200 transform hover:-translate-y-0.5">
                        Go to Dashboard
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>

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
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 mb-4">
                                Please provide your student information to generate your personalized QR code.
                            </p>
                            
                            <form id="qrRegistrationForm" class="space-y-4">
                                @csrf
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
                                
                                <div>
                                    <label for="MI" class="block text-sm font-medium text-gray-700">Middle Initial</label>
                                    <input type="text" id="MI" name="MI" required
                                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-200">
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
                                
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                                    <input type="email" id="email" name="email" required
                                        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                                </div>
                                
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
    const form = document.getElementById('qrRegistrationForm');
    const formErrors = document.getElementById('formErrors');
    const errorMessage = document.getElementById('errorMessage');
    
    // Show modal
    registerBtn.addEventListener('click', () => {
        modal.classList.remove('hidden');
    });
    
    // Hide modal
    function hideModal() {
        modal.classList.add('hidden');
        form.reset();
        formErrors.classList.add('hidden');
    }
    
    // Cancel button
    cancelBtn.addEventListener('click', hideModal);
    
    // Submit form
    submitBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Show loading spinner
        const loadingModal = document.getElementById('loadingModal');
        loadingModal.classList.remove('hidden');
        
        // Hide any previous errors
        formErrors.classList.add('hidden');
        
        // Get form data
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
</script>
</body>
</html>
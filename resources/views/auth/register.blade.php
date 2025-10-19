<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register - CSU Library</title>
    <link rel="icon" type="image/x-icon" href="/images/library.png">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background: #1a1a1a; /* Dark background matching Lovable */
            font-family: 'Figtree', sans-serif;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #6b7280 0%, #9ca3af 50%, #4b5e9e 100%); /* Same gradient as login: gray to blue tones */
        }
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeIn 1s ease-out forwards;
        }
        @keyframes fadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .hover-scale {
            transition: transform 0.3s ease, background-color 0.3s ease;
        }
        .hover-scale:hover {
            transform: scale(1.02);
            background-color: #4b5e9e; /* Adjusted blue on hover */
        }
        .input-focus {
            transition: border-color 0.3s ease;
        }
        .input-focus:focus {
            border-color: #4b5e9e; /* Adjusted blue focus ring */
            outline: none;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col lg:flex-row">
    <!-- Left Section: Gradient Background -->
    <div class="w-full lg:w-1/2 gradient-bg flex items-center justify-center p-6 lg:p-12">
        <div class="text-center text-white fade-in">
            <img src="{{ asset('favicon/library.png') }}" alt="Library Logo" class="w-16 h-16 rounded-full shadow-md mx-auto mb-6">
            <h2 class="text-3xl font-bold">CSU Library</h2>
            <p class="mt-2 text-gray-300">Create your library account</p>
        </div>
    </div>

    <!-- Right Section: Registration Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 lg:p-12 bg-[#1a1a1a] text-white">
        <div class="w-full max-w-md fade-in">
            <!-- Header -->
            <div class="mb-6 text-center">
                <h2 class="text-3xl font-bold">Sign up</h2>
                <p class="text-sm text-gray-400 mt-2">Register to get started</p>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-300">Name</label>
                    <input id="name" name="name" type="text" required autofocus autocomplete="name"
                        value="{{ old('name') }}"
                        class="mt-1 w-full bg-gray-800 border border-gray-600 rounded-lg px-4 py-3 text-sm text-gray-200 input-focus" />
                    @error('name')
                        <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300">Email</label>
                    <input id="email" name="email" type="email" required autocomplete="username"
                        value="{{ old('email') }}"
                        class="mt-1 w-full bg-gray-800 border border-gray-600 rounded-lg px-4 py-3 text-sm text-gray-200 input-focus" />
                    @error('email')
                        <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- User Type -->
                <div class="hidden">
                    <label for="usertype" class="block text-sm font-medium text-gray-300">User Type</label>
                    <select id="usertype" name="usertype" required
                        class="mt-1 w-full bg-gray-800 border border-gray-600 rounded-lg px-4 py-3 text-sm text-gray-200 input-focus">
                        <option value="user" selected>Student</option>
                    </select>
                    @error('usertype')
                        <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="relative">
                    <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
                    <input id="password" name="password" type="password" required autocomplete="new-password"
                        class="mt-1 w-full bg-gray-800 border border-gray-600 rounded-lg px-4 py-3 pr-12 text-sm text-gray-200 input-focus"
                        pattern="(?=.*[a-zA-Z])(?=.*\d).{8,}" title="Password must be at least 8 characters long and contain at least one letter and one number." />
                    <button type="button" id="togglePassword" tabindex="-1" class="absolute right-3 top-9 text-gray-400 hover:text-gray-200 focus:outline-none" style="background: none; border: none; padding: 0;">
                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </button>
                    @error('password')
                        <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="relative">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-300">Confirm Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                        class="mt-1 w-full bg-gray-800 border border-gray-600 rounded-lg px-4 py-3 pr-12 text-sm text-gray-200 input-focus"
                        pattern="(?=.*[a-zA-Z])(?=.*\d).{8,}" title="Password must be at least 8 characters long and contain at least one letter and one number." />
                    <button type="button" id="togglePasswordConfirm" tabindex="-1" class="absolute right-3 top-9 text-gray-400 hover:text-gray-200 focus:outline-none" style="background: none; border: none; padding: 0;">
                        <svg id="eyeIconConfirm" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </button>
                    @error('password_confirmation')
                        <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <a href="{{ route('login') }}" class="text-sm text-indigo-400 hover:text-indigo-300 mb-5 ">
                        Already registered?
                </a>
<!-- Library Policy Agreement -->
<div class="mt-4">
  <label class="inline-flex items-start space-x-2">
    <input id="agreePolicy" type="checkbox" name="agreePolicy" required class="mt-1 text-indigo-500 focus:ring-indigo-500 border-gray-600 rounded">
    <span class="text-sm text-gray-300">
      I have read and agree to the
      <button type="button" onclick="togglePolicyModal()" class="text-indigo-400 hover:text-indigo-300 underline">
        Library Policy
      </button>.
    </span>
  </label>
  <p id="policyError" class="hidden text-red-400 text-sm mt-1">You must agree to the Library Policy to register.</p>
</div>

                <!-- Actions -->
                <div class="flex items-center justify-between flex-col">
                    
                    <button id="registerButton" type="submit"
                        class="px-4 py-3 bg-white text-gray-900 rounded-lg font-medium hover:bg-gray-100 hover-scale focus:outline-none focus:ring-2 focus:ring-indigo-500 w-full">
                        Register
                    </button>
                    <a id="googleRegister" href="{{ route('auth.google.redirect') }}"
                        class="w-full inline-flex justify-center items-center px-4 py-3 bg-white text-gray-900 rounded-lg font-medium hover:bg-red-700 hover-scale mt-3">
                          <img src="https://www.google.com/favicon.ico" alt="Google Icon" class="w-5 h-5 mr-2">

                        Continue with Google
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Policy Modal -->
    <div id="policyModal" class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50">
    <div class="bg-white text-gray-900 rounded-lg shadow-lg max-w-2xl w-full p-6">
        <h3 class="text-lg font-semibold mb-4 text-indigo-600">Library Policy</h3>
        <p class="text-sm text-gray-700 mb-3">Welcome to CSU Library! We're thrilled to have you join our community. By registering, you agree to abide by the following policies to ensure a positive and enjoyable experience for everyone:</p>
        <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
        <li>Borrow books responsibly and return them on time to keep our collection circulating smoothly.</li>
        <li>Use your QR code for easy attendance tracking and borrowing transactions.</li>
        <li>Treat all library resources with care and respect to preserve them for future users.</li>
        <li>Maintain a quiet and conducive environment in study areas to support focused learning.</li>
        <li>No food or drinks are allowed in the library to keep our space clean and welcoming.</li>
        <li>Protect your personal information and respect the privacy of others.</li>
        <li>Report any lost items or issues promptly to our staff for assistance.</li>
        <li>Follow all attendance and borrowing rules to maintain fair access for all users.</li>
        </ul>
        <p class="text-sm text-gray-700 mt-3">Thank you for helping us create a great library experience!</p>
        <div class="mt-6 flex justify-end space-x-3">
        <button type="button" onclick="togglePolicyModal()" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Close</button>
        </div>
    </div>
    </div>
</body>

<script>
    const togglePassword = document.getElementById("togglePassword");
    const password = document.getElementById("password");
    const eyeIcon = document.getElementById("eyeIcon");
    let show = false;
    togglePassword.addEventListener("click", (e) => {
        e.preventDefault();
        show = !show;
        password.type = show ? "text" : "password";
        eyeIcon.innerHTML = show
            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-5 0-9-4-9-7s4-7 9-7 9 4 9 7c0 1.306-.835 2.417-2.125 3.825M15 12a3 3 0 11-6 0 3 3 0 016 0z" />'
            : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0a9 9 0 11-18 0 9 9 0 0118 0z" />';
    });
    const togglePasswordConfirm = document.getElementById("togglePasswordConfirm");
    const passwordConfirm = document.getElementById("password_confirmation");
    const eyeIconConfirm = document.getElementById("eyeIconConfirm");
    let showConfirm = false;
    togglePasswordConfirm.addEventListener("click", (e) => {
        e.preventDefault();
        showConfirm = !showConfirm;
        passwordConfirm.type = showConfirm ? "text" : "password";
        eyeIconConfirm.innerHTML = showConfirm
            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-5 0-9-4-9-7s4-7 9-7 9 4 9 7c0 1.306-.835 2.417-2.125 3.825M15 12a3 3 0 11-6 0 3 3 0 016 0z" />'
            : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0a9 9 0 11-18 0 9 9 0 0118 0z" />';
    });

    function togglePolicyModal() {
  const modal = document.getElementById('policyModal');
  modal.classList.toggle('hidden');
}

// Prevent form submission if checkbox is not checked
document.getElementById('registerButton').addEventListener('click', function(e) {
  const agree = document.getElementById('agreePolicy');
  const error = document.getElementById('policyError');
  if (!agree.checked) {
    e.preventDefault();
    error.classList.remove('hidden');
    return false;
  } else {
    error.classList.add('hidden');
  }
});

// Allow form submission when checkbox is checked
document.getElementById('agreePolicy').addEventListener('change', function() {
  const agree = this;
  const error = document.getElementById('policyError');
  if (agree.checked) {
    error.classList.add('hidden');
  }
});

// Prevent Google registration if checkbox is not checked
document.getElementById('googleRegister').addEventListener('click', function(e) {
  const agree = document.getElementById('agreePolicy');
  const error = document.getElementById('policyError');
  if (!agree.checked) {
    e.preventDefault();
    error.classList.remove('hidden');
    return false;
  } else {
    error.classList.add('hidden');
  }
});
</script>
</html>
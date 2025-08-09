<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management - Library System</title>
    <link rel="icon" type="image/x-icon" href="/favicon/Library.png">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

   <script>
        window.assetBaseUrl = "{{ asset('') }}";
    </script>
</head>
<body class="bg-gray-50" x-data="{ sidebarExpanded: true }">
                <x-admin-nav-bar />

    <div class="flex justify-center">
            <div class="mt-10 w-full max-w-2xl p-8 bg-white border border-gray-200 rounded-xl shadow-lg transition-all duration-300">
            <h1 class="text-2xl font-semibold text-gray-900 mb-6 text-center relative">
                Register Student
            </h1>

            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                There were {{ $errors->count() }} errors with your submission
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('admin.students.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @csrf

                {{-- Student ID --}}
                <div>
                    <label for="student_id" class="block text-sm font-medium text-gray-700 mb-1">Student ID</label>
                    <input type="text" id="student_id" name="student_id" value="{{ old('student_id') }}" required
                        class="form-input w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition @error('student_id') border-red-500 @enderror">
                    @error('student_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Last Name --}}
                <div>
                    <label for="lname" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                    <input type="text" id="lname" name="lname" value="{{ old('lname') }}" required
                        class="form-input w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition @error('lname') border-red-500 @enderror">
                    @error('lname')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- First Name --}}
                <div>
                    <label for="fname" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                    <input type="text" id="fname" name="fname" value="{{ old('fname') }}" required
                        class="form-input w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition @error('fname') border-red-500 @enderror">
                    @error('fname')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Middle Initial --}}
                <div>
                    <label for="MI" class="block text-sm font-medium text-gray-700 mb-1">Middle Initial</label>
                    <input type="text" id="MI" name="MI" value="{{ old('MI') }}" required
                        class="form-input w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition @error('MI') border-red-500 @enderror">
                    @error('MI')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- College --}}
                <div>
                    <label for="college" class="block text-sm font-medium text-gray-700 mb-1">College</label>
                    <select id="college" name="college" required
                        class="form-select w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition @error('college') border-red-500 @enderror">
                        <option value="" disabled {{ old('college') ? '' : 'selected' }}>Choose College</option>
                        <option value="CICS" {{ old('college') == 'CICS' ? 'selected' : '' }}>CICS</option>
                        <option value="CTED" {{ old('college') == 'CTED' ? 'selected' : '' }}>CTED</option>
                        <option value="CCJE" {{ old('college') == 'CCJE' ? 'selected' : '' }}>CCJE</option>
                        <option value="CHM" {{ old('college') == 'CHM' ? 'selected' : '' }}>CHM</option>
                        <option value="CBEA" {{ old('college') == 'CBEA' ? 'selected' : '' }}>CBEA</option>
                        <option value="CA" {{ old('college') == 'CA' ? 'selected' : '' }}>CA</option>
                    </select>
                    @error('college')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Year Level --}}
                <div>
                    <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Year Level</label>
                    <input type="number" id="year" name="year" value="{{ old('year') }}" required
                        class="form-input w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition @error('year') border-red-500 @enderror">
                    @error('year')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="md:col-span-2">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        class="form-input w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 transition @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <div class="md:col-span-2 flex justify-center mt-6">
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-md shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        Add Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>



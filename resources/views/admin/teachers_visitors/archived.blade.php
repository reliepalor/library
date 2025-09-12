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
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $teacherVisitor->id }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $teacherVisitor->full_name }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $teacherVisitor->department }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $teacherVisitor->role }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $teacherVisitor->archived_at->format('M d, Y H:i') }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        <form action="{{ route('admin.teachers_visitors.unarchive', $teacherVisitor->id) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit" 
                                                                class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-md text-sm hover:bg-green-200 transition"
                                                                onclick="return confirm('Are you sure you want to unarchive this teacher/visitor?')"
                                                                title="Unarchive">
                                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                                                </svg>
                                                                Unarchive
                                                            </button>
                                                        </form>
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
    </body>
</html> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Admin Dashboard')</title>
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="flex h-screen overflow-hidden">
        @include('layouts.admin_nav')
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="flex items-center justify-between bg-white shadow px-6 py-4">
                <h1 class="text-xl font-semibold text-gray-900">@yield('header', 'Admin Dashboard')</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">{{ Auth::user()->name }}</span>
                    <img src="https://i.pravatar.cc/32" alt="Avatar" class="w-8 h-8 rounded-full" />
                </div>
            </header>
            <main class="flex-1 p-6 bg-gray-100">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>

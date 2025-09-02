<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CSU Library</title>
    <link rel="icon" type="image/x-icon" href="/images/library.png">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('css/user/dashboard.css') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'csu-blue': '#1e3a8a', 
                        'csu-light-blue': '#3b82f6',
                        'csu-accent': '#f59e0b', 
                    },
                    fontFamily: {
                        figtree: ['Figtree', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    <!-- Include Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-figtree bg-gray-50 text-gray-900">

<x-header />

    <div id="home" class="main mt-16">

        <main id="home" class="mt-1">
            <section class="big-hero" style="font-size: 65px;">The Future of <br> Cagayan State University</section>
            <section class="small-hero">Discover our new technology! It’s simple, smart, and designed to help you learn better. </section>
        </main>

        <div class="flex justify-center">
            <div class="container items-center">
                <div class="item item-1"><img src="images/library-images/books2.jpg" alt=""></div>
                <div class="item item-2"><img src="images/library-images/books1.jpg" alt=""></div>
                <div class="item item-3">
                    <div class="hero-btn">
                        <a href="" class="explore">Explore</a>
                    </div>
                    <div>
                        <img src="images/library-images/books4.jpg" alt="">
                    </div>
                </div>
                <div class="item item-4"><img src="images/library-images/books5.jpg" style="aspect-ratio: 1/4;" alt=""></div>
                <div class="item item-5"><img src="images/library-images/books3.jpg" alt=""></div>
            </div>
        </div>
    </div>

    <x-books-component/>

<x-footer />


</body>
</html>
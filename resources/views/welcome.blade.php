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
    <link rel="icon" type="image/x-icon" href="https://via.placeholder.com/32?text=CSU">
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

</head>
<body class="font-figtree bg-gray-50 text-gray-900">

<x-header />

    <main id="home" class="mt-20">
        <section class="big-hero mt-2" style="font-size: 65px;">The Future of <br> Cagayan State University</section>
        <section class="small-hero">Discover our new technology! It’s simple, smart, and designed to help you learn better. </section>
    </main>

    <div class="container">
        <div class="item item-1"><img src="images/library-images/books2.jpg" alt="book-image"></div>
        <div class="item item-2"><img src="images/library-images/books1.jpg" alt="book-image"></div>
        <div class="item item-3">
            <div class="hero-btn">
                <a href="" class="explore">Exploree</a>
                <a href="" class="know">View pages</a>
            </div>
            <div>
                <img src="images/library-images/books4.jpg" alt="">
            </div>
        </div>
        <div class="item item-4"><img src="images/library-images/books5.jpg" style="aspect-ratio: 1/4;" alt=""></div>
        <div class="item item-5"><img src="images/library-images/books3.jpg" alt=""></div>
    </div>


<x-footer />

<!-- JavaScript for Mobile Menu and Scroll Animations -->
<script>
    // Mobile Menu Toggle
    const menuToggle = document.getElementById('menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    menuToggle.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
    });

    // Scroll Animation with Intersection Observer
    const animateElements = document.querySelectorAll('.animate-on-scroll');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    animateElements.forEach(element => observer.observe(element));
</script>
</body>
</html>
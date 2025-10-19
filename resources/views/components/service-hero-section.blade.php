<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Services - Modern Design</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.6s ease-out forwards',
                        'slide-up': 'slideUp 0.8s ease-out forwards',
                    }
                }
            }
        }
    </script>
    
    <style>
        * { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(40px);
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .animate-on-scroll.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body class="bg-white font-inter">

    <!-- Hero Section -->
    <section class="pt-20 pb-16 px-6">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-5xl md:text-6xl font-light text-gray-900 mb-6 tracking-tight">
                Library Services
            </h1>
            <p class="text-xl md:text-2xl font-light text-gray-600 max-w-3xl mx-auto leading-relaxed">
                Designed to enhance your learning journey with cutting-edge technology and seamless experiences.
            </p>
        </div>
    </section>

    <!-- Services Carousel Section -->
    <section class="py-20 px-6 bg-gray-50">
        <div class="max-w-7xl mx-auto">
            
            <!-- Section Header -->
            <div class="text-center mb-16 animate-on-scroll">
                <h2 class="text-4xl md:text-5xl font-light text-gray-900 mb-4">
                    Three powerful ways to connect
                </h2>
                <p class="text-lg text-gray-600 font-light">
                    Everything you need for your library experience
                </p>
            </div>

            <!-- Carousel Container -->
            <div class="relative overflow-hidden">
                <div class="flex transition-transform duration-700 ease-in-out" id="carousel">
                    
                    <!-- Service 1: QR Code Attendance -->
                    <div class="w-full flex-shrink-0 px-4">
                        <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-16 items-center">
                            <!-- Content -->
                            <div class="space-y-8 animate-on-scroll">
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-2xl">
                                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-3xl md:text-4xl font-light text-gray-900 mb-4">
                                        QR Code Attendance
                                    </h3>
                                    <p class="text-lg text-gray-600 font-light leading-relaxed mb-8">
                                        Streamline your library visits with instant check-in. Simply scan and go—no waiting, no hassle.
                                    </p>
                                    <ul class="space-y-3 mb-8">
                                        <li class="flex items-center text-gray-700">
                                            <div class="w-1.5 h-1.5 bg-green-500 rounded-full mr-4"></div>
                                            Instant check-in process
                                        </li>
                                        <li class="flex items-center text-gray-700">
                                            <div class="w-1.5 h-1.5 bg-green-500 rounded-full mr-4"></div>
                                            Accurate attendance records
                                        </li>
                                        <li class="flex items-center text-gray-700">
                                            <div class="w-1.5 h-1.5 bg-green-500 rounded-full mr-4"></div>
                                            Reduced waiting times
                                        </li>
                                    </ul>
                                    <a href="#" class="inline-flex items-center text-blue-600 font-medium hover:text-blue-700 transition-colors group">
                                        Learn more
                                        <svg class="ml-2 w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            <!-- Visual -->
                            <div class="flex justify-center animate-on-scroll">
                                <div class="w-80 h-80 bg-gradient-to-br from-blue-50 to-blue-100 rounded-3xl flex items-center justify-center">
                                    <div class="w-40 h-40 bg-white rounded-2xl shadow-lg flex items-center justify-center">
                                        <svg class="w-20 h-20 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Service 2: Book Management -->
                    <div class="w-full flex-shrink-0 px-4">
                        <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-16 items-center">
                            <!-- Content -->
                            <div class="space-y-8 animate-on-scroll">
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-2xl">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-3xl md:text-4xl font-light text-gray-900 mb-4">
                                        Smart Book Management
                                    </h3>
                                    <p class="text-lg text-gray-600 font-light leading-relaxed mb-8">
                                        Effortlessly borrow and return books with real-time tracking and intelligent recommendations.
                                    </p>
                                    <ul class="space-y-3 mb-8">
                                        <li class="flex items-center text-gray-700">
                                            <div class="w-1.5 h-1.5 bg-green-500 rounded-full mr-4"></div>
                                            Easy reservation system
                                        </li>
                                        <li class="flex items-center text-gray-700">
                                            <div class="w-1.5 h-1.5 bg-green-500 rounded-full mr-4"></div>
                                            Real-time inventory tracking
                                        </li>
                                        <li class="flex items-center text-gray-700">
                                            <div class="w-1.5 h-1.5 bg-green-500 rounded-full mr-4"></div>
                                            Automated due date reminders
                                        </li>
                                    </ul>
                                    <a href="#" class="inline-flex items-center text-green-600 font-medium hover:text-green-700 transition-colors group">
                                        Browse books
                                        <svg class="ml-2 w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            <!-- Visual -->
                            <div class="flex justify-center animate-on-scroll">
                                <div class="w-80 h-80 bg-gradient-to-br from-green-50 to-green-100 rounded-3xl flex items-center justify-center">
                                    <div class="w-40 h-40 bg-white rounded-2xl shadow-lg flex items-center justify-center">
                                        <svg class="w-20 h-20 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Service 3: Notifications -->
                    <div class="w-full flex-shrink-0 px-4">
                        <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-16 items-center">
                            <!-- Content -->
                            <div class="space-y-8 animate-on-scroll">
                                <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-100 rounded-2xl">
                                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-3xl md:text-4xl font-light text-gray-900 mb-4">
                                        Smart Notifications
                                    </h3>
                                    <p class="text-lg text-gray-600 font-light leading-relaxed mb-8">
                                        Stay informed with intelligent alerts that keep you connected to your library activities.
                                    </p>
                                    <ul class="space-y-3 mb-8">
                                        <li class="flex items-center text-gray-700">
                                            <div class="w-1.5 h-1.5 bg-green-500 rounded-full mr-4"></div>
                                            Due date reminders
                                        </li>
                                        <li class="flex items-center text-gray-700">
                                            <div class="w-1.5 h-1.5 bg-green-500 rounded-full mr-4"></div>
                                            Overdue notifications
                                        </li>
                                        <li class="flex items-center text-gray-700">
                                            <div class="w-1.5 h-1.5 bg-green-500 rounded-full mr-4"></div>
                                            Library updates
                                        </li>
                                    </ul>
                                    <a href="#" class="inline-flex items-center text-purple-600 font-medium hover:text-purple-700 transition-colors group">
                                        Manage preferences
                                        <svg class="ml-2 w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            <!-- Visual -->
                            <div class="flex justify-center animate-on-scroll">
                                <div class="w-80 h-80 bg-gradient-to-br from-purple-50 to-purple-100 rounded-3xl flex items-center justify-center">
                                    <div class="w-40 h-40 bg-white rounded-2xl shadow-lg flex items-center justify-center">
                                        <svg class="w-20 h-20 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Carousel Navigation -->
                <div class="flex justify-center mt-12 space-x-2">
                    <button class="carousel-dot w-2 h-2 rounded-full bg-gray-900 transition-all duration-300" data-slide="0"></button>
                    <button class="carousel-dot w-2 h-2 rounded-full bg-gray-300 transition-all duration-300" data-slide="1"></button>
                    <button class="carousel-dot w-2 h-2 rounded-full bg-gray-300 transition-all duration-300" data-slide="2"></button>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 px-6">
        <div class="max-w-6xl mx-auto">
            
            <!-- Header -->
            <div class="text-center mb-16 animate-on-scroll">
                <h2 class="text-4xl md:text-5xl font-light text-gray-900 mb-6">
                    Why choose our services?
                </h2>
                <p class="text-lg text-gray-600 font-light max-w-2xl mx-auto">
                    Built with you in mind, combining cutting-edge technology with intuitive design.
                </p>
            </div>

            <!-- Features Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-12">
                
                <div class="text-center animate-on-scroll">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-blue-100 rounded-xl mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Lightning Fast</h3>
                    <p class="text-gray-600 font-light text-sm leading-relaxed">
                        Optimized for speed, ensuring you spend less time waiting.
                    </p>
                </div>

                <div class="text-center animate-on-scroll">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-green-100 rounded-xl mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Secure & Private</h3>
                    <p class="text-gray-600 font-light text-sm leading-relaxed">
                        Protected with industry-standard security measures.
                    </p>
                </div>

                <div class="text-center animate-on-scroll">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-purple-100 rounded-xl mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Always Available</h3>
                    <p class="text-gray-600 font-light text-sm leading-relaxed">
                        Access our services 24/7 from any device.
                    </p>
                </div>

                <div class="text-center animate-on-scroll">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-orange-100 rounded-xl mb-4">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Intuitive Design</h3>
                    <p class="text-gray-600 font-light text-sm leading-relaxed">
                        Designed to make your experience seamless.
                    </p>
                </div>

            </div>
        </div>
    </section>

    <!-- QR Code Section -->
    <section class="py-20 px-6 bg-gray-50">
        <div class="max-w-5xl mx-auto text-center">
            
            <!-- Header -->
            <div class="mb-16 animate-on-scroll">
                <h2 class="text-4xl md:text-5xl font-light text-gray-900 mb-6">
                    Get your personalized QR code
                </h2>
                <p class="text-lg text-gray-600 font-light max-w-2xl mx-auto">
                    Register now for quick access, book borrowing, and attendance tracking.
                </p>
            </div>

            <!-- Benefits -->
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
                
                <div class="text-center animate-on-scroll">
                    <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3"/>
                        </svg>
                    </div>
                    <p class="text-gray-700 font-light">Quick check-in at library entrance</p>
                </div>

                <div class="text-center animate-on-scroll">
                    <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5"/>
                        </svg>
                    </div>
                    <p class="text-gray-700 font-light">Easy book borrowing and returning</p>
                </div>

                <div class="text-center animate-on-scroll">
                    <div class="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6"/>
                        </svg>
                    </div>
                    <p class="text-gray-700 font-light">Attendance tracking for study sessions</p>
                </div>

                <div class="text-center animate-on-scroll">
                    <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <p class="text-gray-700 font-light">Personalized library experience</p>
                </div>

            </div>

            <!-- CTA Button -->
            <div class="animate-on-scroll">
                <button id="registerQrBtn" class="inline-flex items-center px-8 py-4 bg-gray-900 text-white font-medium rounded-full transition-all duration-300 hover:bg-gray-800 hover:scale-105 group">
                    Register for QR Code
                    <svg class="ml-3 w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </button>
            </div>

        </div>
    </section>



    <script>
        // Carousel functionality
        let currentSlide = 0;
        const slides = document.querySelectorAll('#carousel > div');
        const dots = document.querySelectorAll('.carousel-dot');
        const carousel = document.getElementById('carousel');

        function updateCarousel() {
            carousel.style.transform = `translateX(-${currentSlide * 100}%)`;
            
            dots.forEach((dot, index) => {
                if (index === currentSlide) {
                    dot.classList.remove('bg-gray-300');
                    dot.classList.add('bg-gray-900');
                } else {
                    dot.classList.remove('bg-gray-900');
                    dot.classList.add('bg-gray-300');
                }
            });
        }

        // Dot navigation
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                currentSlide = index;
                updateCarousel();
            });
        });

        // Auto-advance carousel
        setInterval(() => {
            currentSlide = (currentSlide + 1) % slides.length;
            updateCarousel();
        }, 8000);

        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        // Observe all animated elements
        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            observer.observe(el);
        });
    </script>

</body>
</html>
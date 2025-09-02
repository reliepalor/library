<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern CTA Section</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/user/dashboard.css') }}">

    <style>
        /* Modern Minimalist Animations */
        .fade-in-up {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .fade-in-up.animate {
            opacity: 1;
            transform: translateY(0);
        }

        /* Staggered animation delays */
        .fade-in-up:nth-child(1) { transition-delay: 0.1s; }
        .fade-in-up:nth-child(2) { transition-delay: 0.3s; }
        .fade-in-up:nth-child(3) { transition-delay: 0.5s; }
        .fade-in-up:nth-child(4) { transition-delay: 0.7s; }

        /* Subtle background pattern */
        .pattern-overlay {
            background-image: 
                radial-gradient(circle at 25% 25%, rgba(255, 255, 255, 0.02) 2px, transparent 2px),
                radial-gradient(circle at 75% 75%, rgba(255, 255, 255, 0.02) 2px, transparent 2px);
            background-size: 60px 60px;
            background-position: 0 0, 30px 30px;
        }

        /* Modern button hover effects */
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.6s;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
        }

        /* Stats counter animation */
        .stat-number {
            font-variant-numeric: tabular-nums;
        }

        /* Subtle glow effect */
        .glow-subtle {
            box-shadow: 0 0 40px rgba(102, 126, 234, 0.1);
        }

        /* Modern gradient background */
        .modern-gradient {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 25%, #1e293b 75%, #0f172a 100%);
        }

        /* Intersection Observer Animation Trigger */
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .animate-on-scroll.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Responsive text sizing */
        .heading-responsive {
            font-size: clamp(1.875rem, 4vw, 3rem);
            line-height: 1.2;
        }

        .text-responsive {
            font-size: clamp(1rem, 2.5vw, 1.25rem);
            line-height: 1.6;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    
    <div class="flex justify-center mb-5 w-full">
        <section class="modern-gradient pattern-overlay glow-subtle relative py-16 sm:py-20 px-6 sm:px-8 lg:px-12 overflow-hidden mt-12 rounded-3xl w-full max-w-7xl">
            
            <!-- Subtle gradient overlay -->
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-900/20 via-transparent to-slate-900/30 rounded-3xl"></div>
            
            <!-- Content Container -->
            <div class="relative max-w-4xl mx-auto text-center space-y-8">
                
                <!-- Main Heading -->
                <h1 class="heading-responsive font-light text-white animate-on-scroll tracking-tight">
                    Ready to Explore the 
                    <span class="font-medium bg-gradient-to-r from-indigo-300 to-purple-300 bg-clip-text text-transparent">
                        World of Books
                    </span>?
                </h1>
                
                <!-- Subtitle -->
                <p class="text-responsive text-slate-300 max-w-2xl mx-auto animate-on-scroll font-light leading-relaxed">
                    Take the first step today. Find your ideal book, build knowledge, and join thousands of avid readers.
                </p>
                
                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-center gap-4 animate-on-scroll">
                    <a href={{route('user.books.index')}} class="btn-primary inline-flex items-center justify-center px-8 py-4 text-white font-medium rounded-2xl transition-all duration-300 hover:scale-105 hover:shadow-2xl group">
                        <i class="fas fa-book mr-3 transition-transform group-hover:rotate-12"></i>
                        Find Your Book
                    </a>
                    <a href={{route('user.attendance.index')}} class="btn-secondary inline-flex items-center justify-center px-8 py-4 text-white font-medium rounded-2xl transition-all duration-300 hover:scale-105 group">
                        <i class="fas fa-tachometer-alt mr-3 transition-transform group-hover:scale-110"></i>
                        Borrow some Book
                    </a>
                </div>
                
                <!-- Statistics -->
                <div class="flex flex-col sm:flex-row justify-center items-center gap-8 sm:gap-12 pt-6 animate-on-scroll">
                    <div class="flex items-center text-slate-400 hover:text-white transition-colors duration-300">
                        <div class="w-2 h-2 bg-indigo-400 rounded-full mr-3 animate-pulse"></div>
                        <i class="fas fa-users mr-2"></i>
                        <span class="stat-number font-medium">5,000+</span>
                        <span class="ml-1 font-light">Members</span>
                    </div>
                    
                    <div class="flex items-center text-slate-400 hover:text-white transition-colors duration-300">
                        <div class="w-2 h-2 bg-purple-400 rounded-full mr-3 animate-pulse" style="animation-delay: 0.5s;"></div>
                        <i class="fas fa-book-open mr-2"></i>
                        <span class="stat-number font-medium">50+</span>
                        <span class="ml-1 font-light">Categories</span>
                    </div>
                    
                    <div class="flex items-center text-slate-400 hover:text-white transition-colors duration-300">
                        <div class="w-2 h-2 bg-emerald-400 rounded-full mr-3 animate-pulse" style="animation-delay: 1s;"></div>
                        <i class="fas fa-check-circle mr-2"></i>
                        <span class="stat-number font-medium">98%</span>
                        <span class="ml-1 font-light">Satisfaction</span>
                    </div>
                </div>
                
            </div>
            
            <!-- Decorative Elements -->
            <div class="absolute top-10 left-10 w-20 h-20 bg-gradient-to-br from-indigo-400/10 to-purple-400/10 rounded-full blur-xl"></div>
            <div class="absolute bottom-10 right-10 w-32 h-32 bg-gradient-to-br from-purple-400/10 to-pink-400/10 rounded-full blur-xl"></div>
            
        </section>
    </div>

    <script>
        // Modern Intersection Observer for scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    // Add staggered delay based on element index
                    setTimeout(() => {
                        entry.target.classList.add('visible');
                    }, index * 200);
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe all elements with scroll animation
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.animate-on-scroll').forEach(el => {
                observer.observe(el);
            });
        });

        // Optional: Counter animation for statistics
        function animateCounter(element, target, duration = 2000) {
            let start = 0;
            const increment = target / (duration / 16);
            
            const timer = setInterval(() => {
                start += increment;
                if (start >= target) {
                    element.textContent = target.toLocaleString();
                    clearInterval(timer);
                } else {
                    element.textContent = Math.floor(start).toLocaleString();
                }
            }, 16);
        }

        // Trigger counter animations when section becomes visible
        const statsSection = document.querySelector('.animate-on-scroll');
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // Animate counters if needed
                    const counters = entry.target.querySelectorAll('.stat-number');
                    counters.forEach((counter, index) => {
                        const text = counter.textContent;
                        const number = parseInt(text.replace(/\D/g, ''));
                        if (number > 10) {
                            setTimeout(() => {
                                counter.textContent = '0';
                                animateCounter(counter, number);
                            }, index * 300);
                        }
                    });
                    statsObserver.unobserve(entry.target);
                }
            });
        });

        if (statsSection) {
            statsObserver.observe(statsSection);
        }
    </script>

</body>
</html>
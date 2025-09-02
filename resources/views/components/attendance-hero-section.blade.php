<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Hero Section</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        /* Modern font family */
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        /* Subtle fade-in animations */
        .fade-in {
            opacity: 0;
            transform: translateY(24px);
            animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        .fade-in-delayed {
            opacity: 0;
            transform: translateY(24px);
            animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            animation-delay: 0.3s;
        }

        .fade-in-slow {
            opacity: 0;
            transform: translateY(24px);
            animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            animation-delay: 0.5s;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Subtle typing cursor */
        .typing-cursor::after {
            content: '|';
            animation: blink 1s infinite;
            color: #6b7280;
            margin-left: 2px;
        }

        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0; }
        }

        /* Modern button hover effects */
        .modern-button {
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .modern-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
            transition: left 0.5s;
        }

        .modern-button:hover::before {
            left: 100%;
        }

        .modern-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        /* Subtle background texture */
        .subtle-texture {
            background-image: 
                radial-gradient(circle at 25% 25%, rgba(0, 0, 0, 0.01) 2px, transparent 2px),
                radial-gradient(circle at 75% 75%, rgba(0, 0, 0, 0.01) 2px, transparent 2px);
            background-size: 80px 80px;
            background-position: 0 0, 40px 40px;
        }

        /* Responsive typography */
        .hero-title {
            font-size: clamp(2rem, 5vw, 3.5rem);
            line-height: 1.1;
            font-weight: 400;
            letter-spacing: -0.025em;
        }

        .hero-subtitle {
            font-size: clamp(1rem, 2.5vw, 1.25rem);
            line-height: 1.6;
            font-weight: 300;
        }

        /* Subtle gradient accents */
        .accent-gradient {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        }

        .text-accent {
            background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Minimal divider */
        .divider-line {
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #d1d5db, transparent);
            animation: expandLine 1s ease-out forwards;
            animation-delay: 0.8s;
        }

        @keyframes expandLine {
            to { width: 60px; }
        }

        /* Stats counter styling */
        .stat-item {
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-2px);
        }

        .stat-number {
            font-variant-numeric: tabular-nums;
            font-weight: 600;
        }
    </style>
</head>
<body class="bg-white">
    
    <section class="relative bg-gradient-to-b from-gray-50 to-white py-16 sm:py-20 px-4 sm:px-6 lg:px-8 max-w-8xl mx-auto overflow-hidden">
        
        <!-- Subtle background texture -->
        <div class="absolute inset-0 subtle-texture opacity-30"></div>
        
        <!-- Content Container -->
        <div class="relative max-w-5xl mx-auto text-center">
            
            <!-- Main Heading -->
            <div class="fade-in mb-8">
                <h1 class="hero-title text-gray-800 mb-6">
                    Digital Library Attendance
                </h1>
                <div class="divider-line mx-auto mb-6"></div>
                <p class="text-lg text-gray-600 font-light">
                    makes your experience smoother through
                </p>
            </div>
            
            <!-- Dynamic Text Section -->
            <div class="fade-in-delayed mb-12">
                <div class="inline-flex items-center justify-center">
                    <span class="text-2xl sm:text-3xl font-medium text-accent typing-cursor" id="dynamic-text"></span>
                </div>
            </div>
            
            <!-- Subtitle -->
            <div class="fade-in-delayed mb-12">
                <p class="hero-subtitle text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    Whether you're here to focus on your studies, borrow your favorite books, or explore other activities, 
                    we make your library journey <span class="font-medium text-gray-800">easier</span>, <span class="font-medium text-gray-800">smarter</span>, and <span class="font-medium text-gray-800">more connected</span>.
                </p>
            </div>
            
            <!-- CTA Button -->
            <div class="fade-in-slow mb-16">
                <a href="#get-started" 
                   class="modern-button inline-flex items-center px-8 py-4 bg-gray-800 text-white font-medium rounded-2xl transition-all duration-300 hover:bg-gray-900 group">
                    <span>Get Started</span>
                    <svg class="ml-2 w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            
            
        </div>
        
        <!-- Subtle bottom fade -->
        <div class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-white to-transparent pointer-events-none"></div>
        
    </section>

    <script>
        // Modern typing effect with smoother transitions
        class TypeWriter {
            constructor(textElement, words, typeSpeed = 150, deleteSpeed = 100, delayBetween = 2000) {
                this.textElement = textElement;
                this.words = words;
                this.typeSpeed = typeSpeed;
                this.deleteSpeed = deleteSpeed;
                this.delayBetween = delayBetween;
                this.currentWordIndex = 0;
                this.currentText = '';
                this.isDeleting = false;
                this.start();
            }

            start() {
                this.type();
            }

            type() {
                const currentWord = this.words[this.currentWordIndex];
                
                if (this.isDeleting) {
                    this.currentText = currentWord.substring(0, this.currentText.length - 1);
                } else {
                    this.currentText = currentWord.substring(0, this.currentText.length + 1);
                }

                this.textElement.textContent = this.currentText;

                let typeSpeed = this.isDeleting ? this.deleteSpeed : this.typeSpeed;

                if (!this.isDeleting && this.currentText === currentWord) {
                    typeSpeed = this.delayBetween;
                    this.isDeleting = true;
                } else if (this.isDeleting && this.currentText === '') {
                    this.isDeleting = false;
                    this.currentWordIndex = (this.currentWordIndex + 1) % this.words.length;
                    typeSpeed = 500;
                }

                setTimeout(() => this.type(), typeSpeed);
            }
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            const phrases = [
                "Stay to Study",
                "Borrow Books", 
                "Stay and Borrow",
                "Other Activities"
            ];

            const textElement = document.getElementById("dynamic-text");
            if (textElement) {
                new TypeWriter(textElement, phrases, 120, 80, 2000);
            }

            // Add intersection observer for scroll-based animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animationPlayState = 'running';
                    }
                });
            }, observerOptions);

            // Observe fade-in elements
            document.querySelectorAll('.fade-in, .fade-in-delayed, .fade-in-slow').forEach(el => {
                observer.observe(el);
            });
        });
    </script>

</body>
</html>
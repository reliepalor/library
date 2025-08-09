<section class="relative bg-gradient-to-br from-sky-50 via-white to-blue-50 py-12 sm:py-16 px-4 sm:px-6 lg:px-8 overflow-hidden mb-5 rounded-xl shadow-md">
    <!-- Background pattern -->
    <div class="absolute inset-0 bg-[url('https://www.toptal.com/designers/subtlepatterns/patterns/book-pattern.png')] opacity-10 bg-repeat animate-slow-pan"></div>

    <div class="relative max-w-7xl mx-auto text-center fade-up">
        <!-- Heading -->
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-700 mb-4 sm:mb-6 animate-float">
            Digital Library Attendance
            <span class="block mt-2">
                makes your experience smoother through: <br>
                <span class="inline-block min-w-[14ch] text-blue-700 font-semibold border-b-2 border-blue-100 pb-1 text-center">
                    <span id="dynamic-text"></span>
                </span>
            </span>
        </h1>

        <!-- Subheading -->
        <p class="text-base sm:text-lg lg:text-xl text-gray-600 max-w-3xl mx-auto mb-6 sm:mb-8 fade-up" style="animation-delay: 0.2s;">
            Whether you’re here to focus on your studies, borrow your favorite books, or explore other activities, 
            we make your library journey easier, smarter, and more connected.
        </p>

        <!-- Buttons -->
        <div class="flex flex-col sm:flex-row justify-center gap-4 fade-up" style="animation-delay: 0.4s;">
            <a href="#get-started"
               class="inline-block px-6 py-3 bg-indigo-600 text-white text-sm sm:text-base font-medium rounded-xl shadow-md hover:bg-indigo-700 hover:shadow-lg transition-all duration-300">
                Get Started
            </a>
        </div>
    </div>

    <!-- Bottom gradient fade -->
    <div class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-white to-transparent"></div>
</section>

<style>
    /* Fade Up Animation */
    .fade-up {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeUp 0.8s ease-out forwards;
    }
    @keyframes fadeUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Floating Heading Animation */
    .animate-float {
        animation: float 4s ease-in-out infinite;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-8px); }
    }

    /* Background slow movement */
    .animate-slow-pan {
        animation: pan 40s linear infinite;
    }
    @keyframes pan {
        0% { background-position: 0 0; }
        100% { background-position: 100px 100px; }
    }
</style>

<script>
    const phrases = [
        "Stay to study",
        "Borrow Books",
        "Stay and Borrow",
        "Other Activities"
    ];

    let index = 0;
    let charIndex = 0;
    let typing = true;
    const textEl = document.getElementById("dynamic-text");

    function typeEffect() {
        if (typing) {
            if (charIndex < phrases[index].length) {
                textEl.textContent += phrases[index].charAt(charIndex);
                charIndex++;
                setTimeout(typeEffect, 120);
            } else {
                typing = false;
                setTimeout(typeEffect, 1800);
            }
        } else {
            if (charIndex > 0) {
                textEl.textContent = phrases[index].substring(0, charIndex - 1);
                charIndex--;
                setTimeout(typeEffect, 80);
            } else {
                typing = true;
                index = (index + 1) % phrases.length;
                setTimeout(typeEffect, 300);
            }
        }
    }

    typeEffect();
</script>

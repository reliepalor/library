<html>
    <style>
        .fade {
      transition: opacity 0.5s ease-in-out;
    }
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
    </style>
</html>
<section class="relative bg-gradient-to-br from-sky-50 via-white to-blue-50 py-12 sm:py-16 px-4 sm:px-6 lg:px-8 overflow-hidden mb-5 rounded-xl shadow-md">
            <!-- Background pattern -->
            <div class="absolute inset-0 bg-[url('https://www.toptal.com/designers/subtlepatterns/patterns/book-pattern.png')] opacity-10 bg-repeat"></div>

            <div class="relative max-w-7xl mx-auto text-center">
                <!-- Heading -->
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-gray-700 mb-4 sm:mb-6">
                    Digital Library Attendance
                    <span class="block mt-2">
                    makes your experience smoother through: <br>
                    <span id="dynamic-text" class="fade font-semibold text-underlined">Stay to study</span>
                    </span>
                </h1>

                <!-- Subheading -->
                <p class="text-base sm:text-lg lg:text-xl text-gray-600 max-w-3xl mx-auto mb-6 sm:mb-8">
                    Whether you’re here to focus on your studies, borrow your favorite books, or explore other activities, 
                    we make your library journey easier, smarter, and more connected.
                </p>

                <!-- Buttons -->
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="#get-started"
                    class="inline-block px-6 py-3 bg-indigo-600 text-white text-sm sm:text-base font-medium rounded-xl shadow-md hover:bg-indigo-700 hover:shadow-lg transition-all duration-300">
                        Get Started
                    </a>
                </div>
            </div>

            <!-- Bottom gradient fade -->
            <div class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-white to-transparent"></div>
        </section>

<script>
    const phrases = [
      "Stay to study",
      "Borrow Books",
      "Stay and Borrow",
      "Other Activities"
    ];

    let index = 0;
    const textEl = document.getElementById("dynamic-text");

    setInterval(() => {
      textEl.classList.add("opacity-0");
      setTimeout(() => {
        index = (index + 1) % phrases.length;
        textEl.textContent = phrases[index];
        textEl.classList.remove("opacity-0");
      }, 500); // match fade-out duration
    }, 2000);
</script>
// --- Library Book / E-Book Toggle and Google Books API Search ---
document.addEventListener('DOMContentLoaded', function () {
    const libraryBtn = document.getElementById('library-books-btn');
    const ebookBtn = document.getElementById('ebook-btn');
    const libraryContainer = document.getElementById('library-books-container');
    const ebookContainer = document.getElementById('ebook-container');
    const ebookSearchForm = document.getElementById('ebook-search-form');
    const ebookSearchInput = document.getElementById('ebook-search-input');
    const ebookResults = document.getElementById('ebook-results');

    let ebookTabLoaded = false;

    function setActiveButton(activeBtn, inactiveBtn) {
        activeBtn.classList.add('bg-white', 'text-gray-700', 'shadow', 'active');
        activeBtn.classList.remove('bg-transparent', 'text-gray-500');
        inactiveBtn.classList.remove('bg-white', 'text-gray-700', 'shadow', 'active');
        inactiveBtn.classList.add('bg-transparent', 'text-gray-500');
    }

    if (libraryBtn && ebookBtn && libraryContainer && ebookContainer) {
        libraryBtn.addEventListener('click', function () {
            setActiveButton(libraryBtn, ebookBtn);
            libraryContainer.style.display = 'block';
            ebookContainer.style.display = 'none';
        });
        ebookBtn.addEventListener('click', function () {
            setActiveButton(ebookBtn, libraryBtn);
            libraryContainer.style.display = 'none';
            ebookContainer.style.display = 'block';
            if (!ebookTabLoaded) {
                // Auto-search a default term on first open
                ebookTabLoaded = true;
                ebookSearchInput.value = 'Library';
                ebookSearchForm.dispatchEvent(new Event('submit'));
            }
        });
    }

    // --- Category Buttons Logic ---
    const categoryBtns = document.querySelectorAll('.category-btn');
    categoryBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            // Remove highlight from all
            categoryBtns.forEach(b => b.classList.remove('bg-indigo-500', 'text-white'));
            // Highlight this one
            btn.classList.add('bg-indigo-500', 'text-white');
            // Set search input and trigger search
            ebookSearchInput.value = btn.dataset.category;
            ebookSearchForm.dispatchEvent(new Event('submit'));
        });
    });

    // --- End Category Buttons Logic ---

    if (ebookSearchForm && ebookSearchInput && ebookResults) {
        ebookSearchForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const query = ebookSearchInput.value.trim();
            if (!query) return;
            ebookResults.innerHTML = '<div class="text-center w-full py-8 text-gray-400">Searching...</div>';
            fetch(`https://www.googleapis.com/books/v1/volumes?q=${encodeURIComponent(query)}&maxResults=12`)
                .then(res => res.json())
                .then(data => {
                    if (!data.items || data.items.length === 0) {
                        ebookResults.innerHTML = '<div class="text-center w-full py-8 text-gray-400">No results found.</div>';
                        return;
                    }
                    // Use grid layout for uniform cards
                    ebookResults.className = 'grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6';
                    ebookResults.innerHTML = '';
                    data.items.forEach(item => {
                        const info = item.volumeInfo;
                        const title = info.title || 'No Title';
                        const authors = info.authors ? info.authors.join(', ') : 'Unknown Author';
                        const description = info.description ? info.description : 'No description available.';
                        // Use the highest quality image available
                        let thumbnail = '';
                        if (info.imageLinks) {
                            thumbnail = info.imageLinks.large || info.imageLinks.medium || info.imageLinks.small || info.imageLinks.thumbnail || 'https://via.placeholder.com/200x300?text=No+Cover';
                        } else {
                            thumbnail = 'https://via.placeholder.com/200x300?text=No+Cover';
                        }
                        const link = info.infoLink || '#';
                        const card = document.createElement('div');
                        card.className = 'relative flex flex-col bg-white rounded-xl border border-gray-200 shadow-md hover:shadow-2xl hover:scale-105 transition-all duration-300 h-96 overflow-hidden group';
                        card.innerHTML = `
                            <div class="relative w-full h-48 bg-gray-100 flex items-center justify-center overflow-hidden group/image">
                                <img src="${thumbnail}" class="object-contain w-full h-full transition-transform duration-300 group-hover/image:scale-105" alt="${title}">
                                <div class="absolute inset-0 bg-black bg-opacity-70 text-white opacity-0 group-hover/image:opacity-100 transition-opacity duration-300 flex flex-col justify-center items-center p-4 text-center cursor-pointer">
                                    <div class="text-sm max-h-32 overflow-y-auto">${description}</div>
                                </div>
                            </div>
                            <div class="flex-1 flex flex-col p-4">
                                <h6 class="font-semibold text-base text-gray-800 mb-1 line-clamp-2">${title}</h6>
                                <p class="text-sm text-gray-500 mb-4 line-clamp-1">${authors}</p>
                                <a href="${link}" target="_blank" rel="noopener" class="mt-auto inline-block px-4 py-2 rounded-lg bg-indigo-500 text-white text-xs font-semibold shadow hover:bg-indigo-600 transition-colors text-center">View</a>
                            </div>
                        `;
                        ebookResults.appendChild(card);
                    });
                })
                .catch(() => {
                    ebookResults.innerHTML = '<div class="text-center w-full py-8 text-red-400">Error fetching results.</div>';
                });
        });
    }
});
// --- End Google Books API Search ---

// --- Library Book Search Filtering ---
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('library-search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const query = searchInput.value.trim().toLowerCase();
            document.querySelectorAll('#library-books-container .grid > div').forEach(card => {
                // Find title, author, and book code text
                const title = card.querySelector('h3')?.textContent?.toLowerCase() || '';
                const author = card.querySelector('p.text-sm.text-gray-600')?.textContent?.toLowerCase() || '';
                const code = card.querySelector('p.text-md')?.textContent?.toLowerCase() || '';
                // Section filter (from Alpine)
                const section = card.querySelector('span.absolute.top-2.right-2')?.textContent?.trim() || '';
                // Get selectedFilter from Alpine
                let selectedFilter = 'all';
                try {
                    selectedFilter = document.body.__x.$data.selectedFilter;
                } catch {}
                // Show/hide based on search and filter
                const matchesSearch = !query || title.includes(query) || author.includes(query) || code.includes(query);
                const matchesSection = selectedFilter === 'all' || section === selectedFilter;
                card.style.display = (matchesSearch && matchesSection) ? '' : 'none';
            });
        });
        // Also re-filter when section changes (Alpine)
        document.addEventListener('alpine:init', () => {
            Alpine.effect(() => {
                const selectedFilter = Alpine.store('selectedFilter') || 'all';
                searchInput.dispatchEvent(new Event('input'));
            });
        });
    }
});
// --- End Library Book Search Filtering ---
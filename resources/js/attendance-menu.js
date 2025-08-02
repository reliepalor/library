  const dropdownButton = document.getElementById('dropdownButton');
    const dropdownMenu = document.getElementById('dropdownMenu');

    dropdownButton.addEventListener('click', (e) => {
        e.stopPropagation();
        const isExpanded = dropdownButton.getAttribute('aria-expanded') === 'true';
        dropdownMenu.classList.toggle('show', !isExpanded);
        dropdownButton.setAttribute('aria-expanded', !isExpanded);
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!dropdownButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
            dropdownMenu.classList.remove('show');
            dropdownButton.setAttribute('aria-expanded', 'false');
        }
    });

    // Close dropdown when an item is selected
    dropdownMenu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            dropdownMenu.classList.remove('show');
            dropdownButton.setAttribute('aria-expanded', 'false');
        });
    });

    // Keyboard accessibility
    dropdownButton.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            dropdownButton.click();
        }
    });

    // Ensure links are visible when menu opens
    dropdownMenu.addEventListener('transitionend', () => {
        if (dropdownMenu.classList.contains('show')) {
            dropdownMenu.querySelectorAll('a').forEach((link, index) => {
                link.style.opacity = '1';
                link.style.animationDelay = `${(index + 1) * 0.1}s`;
            });
        }
    });
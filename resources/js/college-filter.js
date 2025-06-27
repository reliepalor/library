document.addEventListener('DOMContentLoaded', function () {
    console.log('college-filter.js loaded');
    const button = document.getElementById('collegeFilterButton');
    const menu = document.getElementById('collegeFilterMenu');
    const selectedCollege = document.getElementById('selectedCollege');
    const options = document.querySelectorAll('.college-filter-option');
    const applyFiltersButton = document.getElementById('applyFiltersButton');

    if (!button || !menu || !selectedCollege || !options || !applyFiltersButton) {
        console.error('Elements not found. Check IDs: collegeFilterButton, collegeFilterMenu, selectedCollege, college-filter-option, applyFiltersButton');
        return;
    }

    // Filter function (adapted to filter student table rows)
    function filterColleges(college) {
        console.log('Filtering for college:', college);
        const items = document.querySelectorAll('#student-table-body tr');
        if (!items.length) {
            console.warn('No student rows found with selector: #student-table-body tr');
            return;
        }
        console.log('Found', items.length, 'student rows');
        items.forEach(item => {
            const itemCollege = item.getAttribute('data-college');
            if (!itemCollege) {
                console.warn('Row missing data-college:', item);
            }
            if (college === 'All' || itemCollege === college) {
                item.style.display = '';
                item.classList.remove('hidden');
            } else {
                item.style.display = 'none';
                item.classList.add('hidden');
            }
        });
    }

    // Toggle dropdown
    button.addEventListener('click', function (e) {
        e.stopPropagation();
        const isExpanded = button.getAttribute('aria-expanded') === 'true';
        button.setAttribute('aria-expanded', !isExpanded);
        if (isExpanded) {
            menu.classList.add('hidden', 'opacity-0', 'scale-y-95');
        } else {
            menu.classList.remove('hidden');
            setTimeout(() => {
                menu.classList.remove('opacity-0', 'scale-y-95');
            }, 10);
        }
    });

    // Handle option selection (filter immediately)
    options.forEach(option => {
        option.addEventListener('click', function () {
            const college = this.getAttribute('data-college');
            console.log('Selected college:', college);
            selectedCollege.textContent = college;
            button.setAttribute('data-college', college);
            button.setAttribute('aria-expanded', 'false');
            menu.classList.add('hidden', 'opacity-0', 'scale-y-95');
            filterColleges(college);
            const event = new CustomEvent('collegeFilterChange', { detail: { college } });
            button.dispatchEvent(event);
        });
    });

    // Remove Apply Filters button event listener

    // Close dropdown when clicking outside
    document.addEventListener('click', function (e) {
        if (!button.contains(e.target) && !menu.contains(e.target)) {
            button.setAttribute('aria-expanded', 'false');
            menu.classList.add('hidden', 'opacity-0', 'scale-y-95');
        }
    });

    // Keyboard navigation
    button.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            button.click();
        }
    });

    options.forEach((option, index) => {
        option.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                option.click();
            } else if (e.key === 'ArrowDown') {
                e.preventDefault();
                const next = options[index + 1] || options[0];
                next.focus();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                const prev = options[index - 1] || options[options.length - 1];
                prev.focus();
            } else if (e.key === 'Escape') {
                button.setAttribute('aria-expanded', 'false');
                menu.classList.add('hidden', 'opacity-0', 'scale-y-95');
                button.focus();
            }
        });
    });
});

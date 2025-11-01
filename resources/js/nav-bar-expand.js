// Listen for sidebar toggle events and adjust main content margin
window.addEventListener('sidebarToggled', function(event) {
    const mainContent = document.getElementById('main-content');
    if (event.detail.expanded) {
        mainContent.classList.remove('ml-16');
        mainContent.classList.add('ml-64');
    } else {
        mainContent.classList.remove('ml-64');
        mainContent.classList.add('ml-16');
    }
});
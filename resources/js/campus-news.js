// Campus News specific JavaScript

// Listen for sidebar toggle events and adjust main content margin
window.addEventListener('sidebarToggled', function(event) {
    const mainContent = document.querySelector('.main-content') || document.getElementById('main-content');
    if (event.detail.expanded) {
        mainContent.classList.remove('sidebar-collapsed');
        mainContent.classList.add('sidebar-expanded');
    } else {
        mainContent.classList.remove('sidebar-expanded');
        mainContent.classList.add('sidebar-collapsed');
    }
});

// Image preview function
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.classList.remove('hidden');
        };

        reader.readAsDataURL(input.files[0]);
    } else {
        preview.classList.add('hidden');
    }
}

// Remove current image function (for edit page)
function removeCurrentImage() {
    if (confirm('Are you sure you want to remove the current featured image?')) {
        document.getElementById('removeFeaturedImage').value = '1';
        const currentImageContainer = document.querySelector('.relative.inline-block');
        if (currentImageContainer) {
            currentImageContainer.style.display = 'none';
        }
    }
}

// Confirm delete function (for index page)
function confirmDelete(newsId) {
    if (confirm('Are you sure you want to delete this news article? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/campus-news/${newsId}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Auto-resize textareas
document.addEventListener('DOMContentLoaded', function() {
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    });
});

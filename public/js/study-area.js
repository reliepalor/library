class StudyAreaAvailability {
    constructor() {
        this.availabilityElement = document.getElementById('study-area-availability');
        this.availabilityBadge = document.getElementById('study-area-badge');
        this.maxCapacity = 30;
        this.pollingInterval = 5000; // 5 seconds
        this.initialize();
    }

    initialize() {
        if (!this.availabilityElement) return;
        
        // Initial update
        this.updateAvailability();
        
        // Set up polling
        setInterval(() => this.updateAvailability(), this.pollingInterval);
        
        // Listen for page visibility changes to reduce polling when tab is not active
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                this.updateAvailability();
            }
        });
    }

    async updateAvailability() {
        try {
            const response = await fetch('/api/study-area/availability');
            const data = await response.json();
            
            if (data.success) {
                this.updateUI(data.data);
            }
        } catch (error) {
            console.error('Error updating study area availability:', error);
        }
    }

    updateUI(data) {
        if (!this.availabilityElement) return;

        const { available_slots, status_color, is_full } = data;

        // Update the badge text and class
        this.availabilityElement.textContent = `Available Study Areas: ${available_slots}/${this.maxCapacity}`;

        // Update badge color based on availability
        this.availabilityBadge.className = `badge bg-${status_color} text-white`;

        // Update the available slots span
        const availableSlotsElement = document.getElementById('available-slots');
        if (availableSlotsElement) {
            availableSlotsElement.textContent = available_slots;
        }

        // Update progress bar
        const progressBar = document.getElementById('progress-bar');
        const mobileProgressBar = document.getElementById('mobile-progress-bar');
        const percentage = this.maxCapacity > 0 ? (available_slots / this.maxCapacity) * 100 : 0;

        if (progressBar) {
            progressBar.style.width = `${percentage}%`;
            progressBar.className = `h-full transition-all duration-300 ${
                percentage > 66 ? 'bg-green-500' :
                percentage > 33 ? 'bg-yellow-500' : 'bg-red-500'
            }`;
        }

        if (mobileProgressBar) {
            mobileProgressBar.style.width = `${percentage}%`;
            mobileProgressBar.className = `h-full transition-all duration-300 ${
                percentage > 66 ? 'bg-green-500' :
                percentage > 33 ? 'bg-yellow-500' : 'bg-red-500'
            }`;
        }

        // Update status dot
        const statusDot = document.getElementById('status-dot');
        if (statusDot) {
            statusDot.className = `w-2 h-2 rounded-full mr-2 ${
                percentage > 66 ? 'bg-green-500' :
                percentage > 33 ? 'bg-yellow-500' : 'bg-red-500'
            }`;
        }

        // Show notification if no slots are available
        if (is_full) {
            this.showFullNotification();
        }
    }

    showFullNotification() {
        // Check if notification already exists
        if (document.getElementById('study-area-full-notification')) return;
        
        const notification = document.createElement('div');
        notification.id = 'study-area-full-notification';
        notification.className = 'alert alert-warning alert-dismissible fade show mt-3';
        notification.role = 'alert';
        notification.innerHTML = `
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Warning:</strong> All study areas are currently occupied. Please check back later.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        // Insert after the availability badge
        this.availabilityElement.parentNode.insertBefore(notification, this.availabilityElement.nextSibling);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new StudyAreaAvailability();
});

<!-- Policy Modal -->
<div id="policyModal"
    class="hidden fixed inset-0 bg-black bg-opacity-60 z-50">
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white text-gray-900 rounded-lg shadow-lg max-w-2xl w-full p-6">
        <h3 class="text-lg font-semibold mb-4 text-indigo-600">Library Policy</h3>
        <p class="text-sm text-gray-700 mb-3">
            Welcome to CSU Library! We're thrilled to have you join our community. By registering, you agree to
            abide by the following policies to ensure a positive and enjoyable experience for everyone:
        </p>
        <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
            <li>Borrow books responsibly and return them on time to keep our collection circulating smoothly.</li>
            <li>Use your QR code for easy attendance tracking and borrowing transactions.</li>
            <li>Treat all library resources with care and respect to preserve them for future users.</li>
            <li>Maintain a quiet and conducive environment in study areas to support focused learning.</li>
            <li>No food or drinks are allowed in the library to keep our space clean and welcoming.</li>
            <li>Protect your personal information and respect the privacy of others.</li>
            <li>Report any lost items or issues promptly to our staff for assistance.</li>
            <li>Follow all attendance and borrowing rules to maintain fair access for all users.</li>
        </ul>
        <p class="text-sm text-gray-700 mt-3">Thank you for helping us create a great library experience!</p>
        <div class="mt-6 flex justify-end space-x-3">
            <button type="button" onclick="closePolicyModal()"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">Close</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function togglePolicyModal() {
        const modal = document.getElementById('policyModal');
        if (modal) {
            modal.classList.toggle('hidden');
        }
    }

    function closePolicyModal() {
        const modal = document.getElementById('policyModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    // Make functions globally available
    window.togglePolicyModal = togglePolicyModal;
    window.closePolicyModal = closePolicyModal;

    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('form');
        if (!form) return;

        form.addEventListener('submit', function(e) {
            const agree = document.getElementById('agreePolicy');
            const error = document.getElementById('policyError');
            if (!agree.checked) {
                e.preventDefault();
                error.classList.remove('hidden');
            } else {
                error.classList.add('hidden');
            }
        });
    });
</script>
@endpush

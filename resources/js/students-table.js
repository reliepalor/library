document.addEventListener('DOMContentLoaded', function () {
    // Select all logic
    const selectAllCheckbox = document.getElementById('select-all');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            document.querySelectorAll('.select-student').forEach(cb => {
                const row = cb.closest('tr');
                if (row.style.display !== 'none') {
                    cb.checked = this.checked;
                }
            });
        });
    }

    // Modal logic
    const modal = document.getElementById('batch-print-modal');
    const grid = document.getElementById('batch-print-grid');
    const closeModal = document.getElementById('close-batch-print');
    const modalPrintBtn = document.getElementById('modal-print-btn');

    // Print selected logic (modal version)
    const printSelectedBtn = document.getElementById('print-selected-btn');
    if (printSelectedBtn) {
        printSelectedBtn.addEventListener('click', function() {
            const selected = Array.from(document.querySelectorAll('.select-student:checked'));
            console.log('Selected students:', selected.length);
            if (selected.length === 0) {
                alert('Select at least one student.');
                return;
            }
            if (selected.length > 6) {
                alert('You can only print up to 6 students at a time.');
                return;
            }
            // Prepare data for print
            const students = selected.map(cb => ({
                name: cb.getAttribute('data-name'),
                studentid: cb.getAttribute('data-studentid'),
                college: cb.getAttribute('data-college'),
                qr: cb.getAttribute('data-qr'),
            }));
            // Fill grid
            grid.innerHTML = '';
            students.forEach(stu => {
                grid.innerHTML += `<div class='qr-block bg-white rounded-lg shadow p-4 flex flex-col items-center border'>
                    <div class='name font-semibold text-base mb-1 text-center'>${stu.name}</div>
                    <div class='college text-gray-700 text-sm mb-2 text-center'>${stu.college}</div>
                    <img src='${stu.qr}' alt='QR Code' class='w-32 h-32 bg-white border rounded'>
                </div>`;
            });
            // Fill empty blocks if less than 6
            for(let i=students.length; i<6; i++) {
                grid.innerHTML += `<div class='qr-block'></div>`;
            }
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            modal.style.opacity = 1;
            console.log('Modal opened, grid filled.');
        });
    }

    // Close modal
    if (closeModal) {
        closeModal.addEventListener('click', function() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modal.style.opacity = 0;
        });
    }

    // Print from modal
    if (modalPrintBtn) {
        modalPrintBtn.addEventListener('click', function() {
            modal.classList.add('print-mode');
            window.onafterprint = function() {
                modal.classList.remove('print-mode');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                modal.style.opacity = 0;
                window.onafterprint = null;
            };
            window.print();
        });
    }

    // Close modal on backdrop click
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                modal.style.opacity = 0;
            }
        });
    }

    // QR Code modal logic
    const qrCodeModal = document.getElementById('qr-code-modal');
    const qrCodeModalImg = document.getElementById('qr-code-modal-img');
    const closeQrModalBtn = document.getElementById('close-qr-modal');

    document.querySelectorAll('td.px-4.py-4 img').forEach(img => {
        img.style.cursor = 'pointer';
        img.addEventListener('click', function() {
            qrCodeModalImg.src = this.src;
            document.getElementById('qr-code-modal-name').textContent = this.getAttribute('data-name');
            qrCodeModal.classList.remove('opacity-0', 'pointer-events-none');
            qrCodeModal.classList.add('opacity-100');
            setTimeout(() => {
                qrCodeModal.querySelector('div').classList.remove('scale-95');
                qrCodeModal.querySelector('div').classList.add('scale-100');
            }, 10);
        });
    });

    if (closeQrModalBtn) {
        closeQrModalBtn.addEventListener('click', () => {
            qrCodeModal.querySelector('div').classList.remove('scale-100');
            qrCodeModal.querySelector('div').classList.add('scale-95');
            qrCodeModal.classList.remove('opacity-100');
            qrCodeModal.classList.add('opacity-0', 'pointer-events-none');
        });
    }

    if (qrCodeModal) {
        qrCodeModal.addEventListener('click', (e) => {
            if (e.target === qrCodeModal) {
                closeQrModalBtn.click();
            }
        });
    }
});

"use strict";
(function () {
  document.addEventListener('DOMContentLoaded', function () {
    // Utility: Toast
    const toast = document.getElementById('toast');
    function showToast(message, type = 'success') {
      if (!toast) return;
      toast.textContent = message;
      toast.className = `fixed top-6 right-6 z-50 px-6 py-3 rounded shadow-lg text-white text-base font-medium transition-all duration-300 ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}`;
      toast.classList.remove('hidden');
      setTimeout(() => { toast.classList.add('hidden'); }, 2500);
    }

    // Flash messages (read from data attributes)
    const flash = document.getElementById('flash-data');
    if (flash) {
      const success = flash.dataset.success;
      const error = flash.dataset.error;
      if (success) showToast(success, 'success');
      else if (error) showToast(error, 'error');
    }

    // Charts: Read data from dataset (to be provided in Blade)
    const pageDataEl = document.getElementById('students-page-data');
    const parseJSON = (str) => {
      if (!str) return null;
      try { return JSON.parse(str); } catch (e) { return null; }
    };

    const collegeLabels = pageDataEl ? parseJSON(pageDataEl.dataset.collegeLabels) || [] : [];
    const collegeCounts = pageDataEl ? parseJSON(pageDataEl.dataset.collegeCounts) || [] : [];
    const yearCounts = pageDataEl ? parseJSON(pageDataEl.dataset.yearCounts) || [] : [];

    const pieEl = document.getElementById('collegePieChart');
    if (pieEl && window.Chart) {
      const ctxCollege = pieEl.getContext('2d');
      // eslint-disable-next-line no-undef
      new Chart(ctxCollege, {
        type: 'pie',
        data: {
          labels: collegeLabels,
          datasets: [{
            data: collegeCounts,
            backgroundColor: [
              '#DDD6FE', // total
              '#c77dff', // cics
              '#90e0ef', // cted
              '#ff4d6d', // ccje
              '#ffc8dd', // chm
              '#fae588', // cbea
              '#80ed99'  // ca
            ],
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'right',
              align: 'center',
              labels: {
                usePointStyle: true,
                boxWidth: 12,
                padding: 15,
                font: { size: 12 }
              }
            }
          },
          layout: { padding: { left: 10, right: 10, top: 10, bottom: 10 } }
        }
      });
    }

    const barEl = document.getElementById('yearBarChart');
    if (barEl && window.Chart) {
      const ctxYear = barEl.getContext('2d');
      // eslint-disable-next-line no-undef
      new Chart(ctxYear, {
        type: 'bar',
        data: {
          labels: ['1st Year', '2nd Year', '3rd Year', '4th Year'],
          datasets: [{
            label: 'Students',
            data: yearCounts.length ? yearCounts : [0, 0, 0, 0],
            backgroundColor: '#a2d2ff'
          }]
        },
        options: {
          responsive: true,
          scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
      });
    }

    // College filter logic
    const collegeFilterButton = document.getElementById('collegeFilterButton');
    const collegeFilterMenu = document.getElementById('collegeFilterMenu');
    const selectedCollege = document.getElementById('selectedCollege');
    let currentCollegeFilter = 'All';
    let collegeMenuOpen = false;

    function openCollegeFilterMenu() {
      collegeFilterMenu.classList.remove('hidden', 'opacity-0', 'scale-y-95');
      collegeFilterMenu.classList.add('opacity-100', 'scale-y-100');
      collegeFilterButton.setAttribute('aria-expanded', 'true');
      collegeMenuOpen = true;
    }

    function closeCollegeFilterMenu() {
      collegeFilterMenu.classList.add('opacity-0', 'scale-y-95');
      collegeFilterMenu.classList.remove('opacity-100', 'scale-y-100');
      setTimeout(() => {
        collegeFilterMenu.classList.add('hidden');
        collegeMenuOpen = false;
      }, 300);
      collegeFilterButton.setAttribute('aria-expanded', 'false');
    }

    function toggleCollegeFilterMenu() {
      if (collegeMenuOpen) {
        closeCollegeFilterMenu();
      } else {
        openCollegeFilterMenu();
      }
    }

    if (collegeFilterButton) {
      collegeFilterButton.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        toggleCollegeFilterMenu();
      });
    }

    // Close college menu when clicking outside
    document.addEventListener('click', function (e) {
      if (collegeMenuOpen && !collegeFilterButton.contains(e.target) && !collegeFilterMenu.contains(e.target)) {
        closeCollegeFilterMenu();
      }
    });

    document.querySelectorAll('.college-filter-option').forEach(option => {
      option.addEventListener('click', function (e) {
        e.stopPropagation();
        const college = this.getAttribute('data-college');
        selectedCollege.textContent = college === 'All' ? 'All' : college;
        currentCollegeFilter = college;
        collegeFilterButton.setAttribute('data-college', college);
        closeCollegeFilterMenu();
        applyFilters();
      });
    });

    // Year filter logic
    const yearFilterButton = document.getElementById('yearFilterButton');
    const yearFilterMenu = document.getElementById('yearFilterMenu');
    const selectedYear = document.getElementById('selectedYear');
    let currentYearFilter = 'All';
    let yearMenuOpen = false;

    function openYearFilterMenu() {
      yearFilterMenu.classList.remove('hidden', 'opacity-0', 'scale-y-95');
      yearFilterMenu.classList.add('opacity-100', 'scale-y-100');
      yearFilterButton.setAttribute('aria-expanded', 'true');
      yearMenuOpen = true;
    }

    function closeYearFilterMenu() {
      yearFilterMenu.classList.add('opacity-0', 'scale-y-95');
      yearFilterMenu.classList.remove('opacity-100', 'scale-y-100');
      setTimeout(() => {
        yearFilterMenu.classList.add('hidden');
        yearMenuOpen = false;
      }, 300);
      yearFilterButton.setAttribute('aria-expanded', 'false');
    }

    function toggleYearFilterMenu() {
      if (yearMenuOpen) {
        closeYearFilterMenu();
      } else {
        openYearFilterMenu();
      }
    }

    if (yearFilterButton) {
      yearFilterButton.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        toggleYearFilterMenu();
      });
    }

    // Close year menu when clicking outside
    document.addEventListener('click', function (e) {
      if (yearMenuOpen && !yearFilterButton.contains(e.target) && !yearFilterMenu.contains(e.target)) {
        closeYearFilterMenu();
      }
    });

    document.querySelectorAll('.year-filter-option').forEach(option => {
      option.addEventListener('click', function (e) {
        e.stopPropagation();
        const year = this.getAttribute('data-year');
        selectedYear.textContent = year === 'All' ? 'All' : year + (year === '1' ? 'st' : year === '2' ? 'nd' : year === '3' ? 'rd' : 'th') + ' Year';
        currentYearFilter = year;
        yearFilterButton.setAttribute('data-year', year);
        closeYearFilterMenu();
        applyFilters();
      });
    });

    // Apply filters function
    function applyFilters() {
      const rows = document.querySelectorAll('#student-table-body tr');
      rows.forEach(row => {
        const college = row.getAttribute('data-college');
        const year = row.querySelector('td:nth-child(7)').textContent.trim(); // Year column

        const collegeMatch = currentCollegeFilter === 'All' || college === currentCollegeFilter;
        const yearMatch = currentYearFilter === 'All' || year === currentYearFilter;

        if (collegeMatch && yearMatch) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });

      // Update select all checkbox
      updateSelectAllState();
    }

    // Update select all checkbox state
    function updateSelectAllState() {
      const selectAll = document.getElementById('select-all');
      if (!selectAll) return;

      const visibleCheckboxes = Array.from(document.querySelectorAll('.select-student')).filter(cb => {
        const row = cb.closest('tr');
        return row && row.style.display !== 'none';
      });

      const checkedVisible = visibleCheckboxes.filter(cb => cb.checked).length;
      selectAll.checked = visibleCheckboxes.length > 0 && checkedVisible === visibleCheckboxes.length;
      selectAll.indeterminate = checkedVisible > 0 && checkedVisible < visibleCheckboxes.length;
    }

    // Select all logic
    const selectAll = document.getElementById('select-all');
    if (selectAll) {
      selectAll.addEventListener('change', function () {
        document.querySelectorAll('.select-student').forEach(cb => {
          const row = cb.closest('tr');
          if (!row || row.style.display === 'none') return;
          cb.checked = selectAll.checked;
        });
      });
    }

    // Update select all when individual checkboxes change
    document.addEventListener('change', function (e) {
      if (e.target.classList.contains('select-student')) {
        updateSelectAllState();
      }
    });

    // Batch Print Modal logic
    const modal = document.getElementById('batch-print-modal');
    const grid = document.getElementById('batch-print-grid');
    const closeModal = document.getElementById('close-batch-print');
    const modalPrintBtn = document.getElementById('modal-print-btn');
    const printSelectedBtn = document.getElementById('print-selected-btn');

    function openModal() {
      if (!modal) return;
      modal.classList.remove('hidden');
      modal.classList.add('flex');
      modal.style.opacity = 1;
    }
    function closeModalFn() {
      if (!modal) return;
      modal.classList.add('hidden');
      modal.classList.remove('flex');
      modal.style.opacity = 0;
    }

    if (printSelectedBtn && modal && grid) {
      printSelectedBtn.addEventListener('click', function () {
        const selected = Array.from(document.querySelectorAll('.select-student:checked'));
        if (selected.length === 0) {
          alert('Select at least one student.');
          return;
        }
        if (selected.length > 6) {
          alert('You can only print up to 6 students at a time.');
          return;
        }
        let students = selected.map(cb => ({
          name: cb.getAttribute('data-name') || '',
          studentId: cb.getAttribute('data-student-id') || '',
          college: cb.getAttribute('data-college') || '',
          qr: cb.getAttribute('data-qr') || ''
        }));
        const beforeCount = students.length;
        students = students.filter(stu => !!stu.qr);
        if (students.length === 0) {
          alert('Selected students have no QR codes to print.');
          return;
        }
        if (students.length < beforeCount) {
          alert('Some selected students were skipped because they have no QR code.');
        }
        // Fill grid safely
        grid.innerHTML = '';
        students.forEach(stu => {
          const wrap = document.createElement('div');
          wrap.className = 'qr-block bg-white rounded-lg shadow p-4 flex flex-col items-center border';

          const nameEl = document.createElement('div');
          nameEl.className = 'name font-semibold text-base mb-1 text-center';
          nameEl.textContent = stu.name;

          const collegeEl = document.createElement('div');
          collegeEl.className = 'college text-gray-700 text-sm mb-2 text-center';
          collegeEl.textContent = stu.college;

          const img = document.createElement('img');
          img.className = 'w-32 h-32 bg-white border rounded';
          img.src = stu.qr;
          img.alt = 'QR Code';

          wrap.appendChild(nameEl);
          wrap.appendChild(collegeEl);
          wrap.appendChild(img);
          grid.appendChild(wrap);
        });
        for (let i = students.length; i < 6; i++) {
          const placeholder = document.createElement('div');
          placeholder.className = 'qr-block';
          grid.appendChild(placeholder);
        }
        openModal();
      });
    }

    if (closeModal) {
      closeModal.addEventListener('click', closeModalFn);
    }

    if (modalPrintBtn) {
      modalPrintBtn.addEventListener('click', function () {
        if (!modal) return;
        modal.classList.add('print-mode');
        window.onafterprint = function () {
          modal.classList.remove('print-mode');
          closeModalFn();
          window.onafterprint = null;
        };
        window.print();
      });
    }

    if (modal) {
      modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModalFn();
      });
    }

    // Student search functionality
    const searchInput = document.getElementById('student-search');
    if (searchInput) {
      searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#student-table-body tr');

        rows.forEach(row => {
          const firstName = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
          const lastName = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
          const fullName = `${firstName} ${lastName}`;

          if (fullName.includes(searchTerm) || searchTerm === '') {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        });

        // Update select all checkbox state after filtering
        updateSelectAllState();
      });
    }

    // QR Code modal logic
    const qrCodeModal = document.getElementById('qr-code-modal');
    const qrCodeModalImg = document.getElementById('qr-code-modal-img');
    const closeQrModalBtn = document.getElementById('close-qr-modal');

    function openQrModal(src, name) {
      if (!qrCodeModal || !qrCodeModalImg) return;
      qrCodeModalImg.src = src;
      const nameEl = document.getElementById('qr-code-modal-name');
      if (nameEl) nameEl.textContent = name || '';
      qrCodeModal.classList.remove('opacity-0', 'pointer-events-none');
      qrCodeModal.classList.add('opacity-100');
      setTimeout(() => {
        const inner = qrCodeModal.querySelector('div');
        if (inner) {
          inner.classList.remove('scale-95');
          inner.classList.add('scale-100');
        }
      }, 10);
    }

    function closeQrModal() {
      if (!qrCodeModal) return;
      const inner = qrCodeModal.querySelector('div');
      if (inner) {
        inner.classList.remove('scale-100');
        inner.classList.add('scale-95');
      }
      qrCodeModal.classList.remove('opacity-100');
      qrCodeModal.classList.add('opacity-0', 'pointer-events-none');
    }

    document.querySelectorAll('.qr-thumb').forEach(img => {
      img.style.cursor = 'pointer';
      img.addEventListener('click', function () {
        openQrModal(this.src, this.getAttribute('data-name'));
      });
    });

    if (closeQrModalBtn) {
      closeQrModalBtn.addEventListener('click', closeQrModal);
    }

    if (qrCodeModal) {
      qrCodeModal.addEventListener('click', (e) => {
        if (e.target === qrCodeModal) closeQrModal();
      });
    }
  });
})();

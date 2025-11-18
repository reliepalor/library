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
      if (success && success !== 'null' && success !== '') showToast(success, 'success');
      else if (error && error !== 'null' && error !== '') showToast(error, 'error');
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

    // Gender filter logic
    const genderFilterButton = document.getElementById('genderFilterButton');
    const genderFilterMenu = document.getElementById('genderFilterMenu');
    const selectedGender = document.getElementById('selectedGender');
    let currentGenderFilter = 'All';
    let genderMenuOpen = false;

    function openGenderFilterMenu() {
      genderFilterMenu.classList.remove('hidden', 'opacity-0', 'scale-y-95');
      genderFilterMenu.classList.add('opacity-100', 'scale-y-100');
      genderFilterButton.setAttribute('aria-expanded', 'true');
      genderMenuOpen = true;
    }

    function closeGenderFilterMenu() {
      genderFilterMenu.classList.add('opacity-0', 'scale-y-95');
      genderFilterMenu.classList.remove('opacity-100', 'scale-y-100');
      setTimeout(() => {
        genderFilterMenu.classList.add('hidden');
        genderMenuOpen = false;
      }, 300);
      genderFilterButton.setAttribute('aria-expanded', 'false');
    }

    function toggleGenderFilterMenu() {
      if (genderMenuOpen) {
        closeGenderFilterMenu();
      } else {
        openGenderFilterMenu();
      }
    }

    if (genderFilterButton) {
      genderFilterButton.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        toggleGenderFilterMenu();
      });
    }

    // Close gender menu when clicking outside
    document.addEventListener('click', function (e) {
      if (genderMenuOpen && !genderFilterButton.contains(e.target) && !genderFilterMenu.contains(e.target)) {
        closeGenderFilterMenu();
      }
    });

    document.querySelectorAll('.gender-filter-option').forEach(option => {
      option.addEventListener('click', function (e) {
        e.stopPropagation();
        const gender = this.getAttribute('data-gender');
        selectedGender.textContent = gender === 'All' ? 'All' : gender;
        currentGenderFilter = gender;
        genderFilterButton.setAttribute('data-gender', gender);
        closeGenderFilterMenu();
        applyFilters();
      });
    });

    // Apply filters function
    function applyFilters() {
      const rows = document.querySelectorAll('#student-table-body tr');
      rows.forEach(row => {
        const college = row.getAttribute('data-college');
        const year = row.querySelector('td:nth-child(7)').textContent.trim(); // Year column
        const gender = row.getAttribute('data-gender');

        const collegeMatch = currentCollegeFilter === 'All' || college === currentCollegeFilter;
        const yearMatch = currentYearFilter === 'All' || year === currentYearFilter;
        const genderMatch = currentGenderFilter === 'All' || gender === currentGenderFilter;

        if (collegeMatch && yearMatch && genderMatch) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });

      // Update select all checkbox
      updateSelectAllState();
      updateArchiveSelectedButton();
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
        updateArchiveSelectedButton();
      });
    }

    // Select all archived logic
    const selectAllArchived = document.getElementById('select-all-archived');
    if (selectAllArchived) {
      selectAllArchived.addEventListener('change', function () {
        document.querySelectorAll('.select-archived-student').forEach(cb => {
          cb.checked = selectAllArchived.checked;
        });
        updateDeleteSelectedButton();
      });
    }

    // Update select all when individual checkboxes change
    document.addEventListener('change', function (e) {
      if (e.target.classList.contains('select-student')) {
        updateSelectAllState();
        updateArchiveSelectedButton();
      }
      if (e.target.classList.contains('select-archived-student')) {
        updateSelectAllArchivedState();
        updateDeleteSelectedButton();
      }
    });

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

    // Update select all archived checkbox state
    function updateSelectAllArchivedState() {
      const selectAllArchived = document.getElementById('select-all-archived');
      if (!selectAllArchived) return;

      const archivedCheckboxes = document.querySelectorAll('.select-archived-student');
      const checkedArchived = document.querySelectorAll('.select-archived-student:checked').length;
      selectAllArchived.checked = archivedCheckboxes.length > 0 && checkedArchived === archivedCheckboxes.length;
      selectAllArchived.indeterminate = checkedArchived > 0 && checkedArchived < archivedCheckboxes.length;
    }

    // Update archive selected button visibility
    function updateArchiveSelectedButton() {
      const archiveSelectedBtn = document.getElementById('archive-selected-btn');
      if (!archiveSelectedBtn) return;

      const checkedBoxes = document.querySelectorAll('.select-student:checked');
      if (checkedBoxes.length > 0) {
        archiveSelectedBtn.classList.remove('hidden');
      } else {
        archiveSelectedBtn.classList.add('hidden');
      }
    }

    // Update delete selected button visibility (for archived students only)
    function updateDeleteSelectedButton() {
      const deleteSelectedBtn = document.getElementById('delete-selected-btn');
      if (!deleteSelectedBtn) return;

      const checkedBoxes = document.querySelectorAll('.select-archived-student:checked');
      if (checkedBoxes.length > 0) {
        deleteSelectedBtn.classList.remove('hidden');
      } else {
        deleteSelectedBtn.classList.add('hidden');
      }
    }

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
        let students = selected.map(cb => ({
          name: cb.getAttribute('data-name') || '',
          studentId: cb.getAttribute('data-student-id') || cb.getAttribute('data-studentid') || '',
          college: cb.getAttribute('data-college') || '',
          year: cb.getAttribute('data-year') || '',
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
        // Fill grid dynamically without limit
        grid.innerHTML = '';
        students.forEach(stu => {
           const wrap = document.createElement('div');
           wrap.className = 'qr-block bg-white rounded-lg shadow p-4 flex flex-col items-center border';

           const nameEl = document.createElement('div');
           nameEl.className = 'name font-semibold text-base mb-1 text-center';
           nameEl.textContent = stu.name;

           const collegeEl = document.createElement('div');
           collegeEl.className = 'college text-gray-700 text-sm mb-2 text-center';
           collegeEl.textContent = (stu.college || 'N/A') + ' - ' + (stu.year || 'N/A');

           const img = document.createElement('img');
           img.className = 'w-32 h-32 bg-white border rounded';
           img.src = stu.qr;
           img.alt = 'QR Code';

           wrap.appendChild(nameEl);
           wrap.appendChild(collegeEl);
           wrap.appendChild(img);
           grid.appendChild(wrap);
         });
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
        updateArchiveSelectedButton();
      });
    }

    // Resend QR spinner logic
    document.addEventListener('submit', function (e) {
      const form = e.target;
      if (form.action.includes('resend-qr')) {
        const button = form.querySelector('.resend-qr-btn');
        const spinner = button.querySelector('.spinner');
        if (button && spinner) {
          button.disabled = true;
          spinner.classList.remove('hidden');
        }
      }
    });

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

    // Edit student modal logic
    const editStudentModal = document.getElementById('edit-student-modal');
    const editStudentForm = document.getElementById('edit-student-form');
    const cancelEditStudentBtn = document.getElementById('cancel-edit-student');
    let currentStudentId = null;

    function openEditStudentModal(studentData) {
      if (!editStudentModal || !editStudentForm) return;

      // Populate form fields
      document.getElementById('edit-student_id').value = studentData.student_student_id;
      document.getElementById('edit-lname').value = studentData.lname;
      document.getElementById('edit-fname').value = studentData.fname;
      document.getElementById('edit-MI').value = studentData.mi || '';
      document.getElementById('edit-gender').value = studentData.gender || '';
      document.getElementById('edit-email').value = studentData.email;
      document.getElementById('edit-college').value = studentData.college;
      document.getElementById('edit-year').value = studentData.year;

      currentStudentId = studentData.student_id;
      editStudentModal.classList.remove('hidden');
      editStudentModal.classList.add('flex');
      editStudentModal.style.opacity = 1;
    }

    function closeEditStudentModal() {
      if (!editStudentModal) return;
      editStudentModal.classList.add('hidden');
      editStudentModal.classList.remove('flex');
      editStudentModal.style.opacity = 0;
      currentStudentId = null;
      if (editStudentForm) editStudentForm.reset();
    }

    document.addEventListener('click', function (e) {
      if (e.target.classList.contains('edit-student-btn') || e.target.closest('.edit-student-btn')) {
        const button = e.target.classList.contains('edit-student-btn') ? e.target : e.target.closest('.edit-student-btn');
        const studentData = {
          student_id: button.getAttribute('data-student-id'),
          student_student_id: button.getAttribute('data-student-student-id'),
          lname: button.getAttribute('data-lname'),
          fname: button.getAttribute('data-fname'),
          mi: button.getAttribute('data-mi'),
          gender: button.getAttribute('data-gender'),
          email: button.getAttribute('data-email'),
          college: button.getAttribute('data-college'),
          year: button.getAttribute('data-year')
        };
        openEditStudentModal(studentData);
      }
    });

    if (cancelEditStudentBtn) {
      cancelEditStudentBtn.addEventListener('click', closeEditStudentModal);
    }

    if (editStudentModal) {
      editStudentModal.addEventListener('click', function (e) {
        if (e.target === editStudentModal) closeEditStudentModal();
      });
    }

    if (editStudentForm) {
      editStudentForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(editStudentForm);
        formData.append('_method', 'PUT');

        // Send AJAX request with FormData
        fetch(`/admin/students/${currentStudentId}`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: formData
        })
        .then(response => response.json())
        .then(result => {
          if (result.success) {
            showToast('Student updated successfully!', 'success');
            closeEditStudentModal();
            // Reload the page to show updated data
            setTimeout(() => {
              window.location.reload();
            }, 1500);
          } else {
            showToast(result.message || result.first_error || 'Failed to update student.', 'error');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          showToast('An error occurred while updating the student.', 'error');
        });
      });
    }

    // Archive modal logic
    const archiveModal = document.getElementById('archive-modal');
    const cancelArchiveBtn = document.getElementById('cancel-archive');
    const confirmArchiveBtn = document.getElementById('confirm-archive');
    const archiveModalMessage = document.getElementById('archive-modal-message');
    let currentArchiveForm = null;

    function openArchiveModal(studentName, form) {
      if (!archiveModal || !archiveModalMessage) return;
      archiveModalMessage.textContent = `Are you sure you want to archive ${studentName}?`;
      currentArchiveForm = form;
      archiveModal.classList.remove('hidden');
      archiveModal.classList.add('flex');
      archiveModal.style.opacity = 1;
    }

    function closeArchiveModal() {
      if (!archiveModal) return;
      archiveModal.classList.add('hidden');
      archiveModal.classList.remove('flex');
      archiveModal.style.opacity = 0;
      currentArchiveForm = null;
    }

    document.addEventListener('click', function (e) {
      if (e.target.closest('.archive-btn')) {
        e.preventDefault();
        const button = e.target.closest('.archive-btn');
        const form = button.closest('.archive-form');
        const studentName = form.getAttribute('data-student-name');
        openArchiveModal(studentName, form);
      }
    });

    if (cancelArchiveBtn) {
      cancelArchiveBtn.addEventListener('click', closeArchiveModal);
    }

    if (confirmArchiveBtn) {
      confirmArchiveBtn.addEventListener('click', function () {
        if (currentArchiveForm) {
          currentArchiveForm.submit();
        }
        closeArchiveModal();
      });
    }

    if (archiveModal) {
      archiveModal.addEventListener('click', function (e) {
        if (e.target === archiveModal) closeArchiveModal();
      });
    }
// Toggle archived view logic
const toggleArchivedViewBtn = document.getElementById('toggle-archived-view');
const activeStudentsSection = document.getElementById('active-students-section');
const archivedStudentsSection = document.getElementById('archived-students-section');
const studentsTableTitle = document.getElementById('students-table-title');
let showArchived = false;

function toggleArchivedView() {
  showArchived = !showArchived;

  if (showArchived) {
    // Show Archived
    activeStudentsSection.classList.add('hidden');
    archivedStudentsSection.classList.remove('hidden');
    archivedStudentsSection.style.display = '';
    studentsTableTitle.textContent = '📚 Students';

    const buttonText = toggleArchivedViewBtn.querySelector('.button-text');
    if (buttonText) buttonText.textContent = 'Active Students';

    // Optional visual cue
    toggleArchivedViewBtn.classList.remove('bg-white', 'text-gray-700', 'hover:bg-gray-100');
    toggleArchivedViewBtn.classList.add('bg-blue-600', 'text-white', 'hover:bg-blue-700');

  } else {
    // Show Active
    archivedStudentsSection.classList.add('hidden');
    archivedStudentsSection.style.display = 'none';
    activeStudentsSection.classList.remove('hidden');
    studentsTableTitle.textContent = '👥 Active Students';

    const buttonText = toggleArchivedViewBtn.querySelector('.button-text');
    if (buttonText) buttonText.textContent = 'View Archived';

    // Revert button style
    toggleArchivedViewBtn.classList.remove('bg-blue-600', 'text-white', 'hover:bg-blue-700');
    toggleArchivedViewBtn.classList.add('bg-white', 'text-gray-700', 'hover:bg-gray-100');
  }
}

if (toggleArchivedViewBtn) {
  toggleArchivedViewBtn.addEventListener('click', toggleArchivedView);
}


    // Delete modal logic
    const deleteModal = document.getElementById('delete-modal');
    const cancelDeleteBtn = document.getElementById('cancel-delete');
    const confirmDeleteBtn = document.getElementById('confirm-delete');
    const deleteModalMessage = document.getElementById('delete-modal-message');
    let currentDeleteForm = null;

    function openDeleteModal(studentName, form) {
      if (!deleteModal || !deleteModalMessage) return;
      deleteModalMessage.textContent = `Are you sure you want to permanently delete ${studentName}? This action cannot be undone.`;
      currentDeleteForm = form;
      deleteModal.classList.remove('hidden');
      deleteModal.classList.add('flex');
      deleteModal.style.opacity = 1;
    }

    function closeDeleteModal() {
      if (!deleteModal) return;
      deleteModal.classList.add('hidden');
      deleteModal.classList.remove('flex');
      deleteModal.style.opacity = 0;
      currentDeleteForm = null;
    }

    document.addEventListener('click', function (e) {
      if (e.target.closest('.delete-btn')) {
        e.preventDefault();
        const form = e.target.closest('.delete-form');
        const studentName = form.getAttribute('data-student-name');
        openDeleteModal(studentName, form);
      }
    });

    if (cancelDeleteBtn) {
      cancelDeleteBtn.addEventListener('click', closeDeleteModal);
    }

    if (confirmDeleteBtn) {
      confirmDeleteBtn.addEventListener('click', function () {
        if (currentDeleteForm) {
          currentDeleteForm.submit();
        }
        closeDeleteModal();
      });
    }

    if (deleteModal) {
      deleteModal.addEventListener('click', function (e) {
        if (e.target === deleteModal) closeDeleteModal();
      });
    }

    // Archive selected students logic
    const archiveSelectedBtn = document.getElementById('archive-selected-btn');
    if (archiveSelectedBtn) {
      archiveSelectedBtn.addEventListener('click', function () {
        const selectedCheckboxes = document.querySelectorAll('.select-student:checked');
        if (selectedCheckboxes.length === 0) {
          showToast('No students selected.', 'error');
          return;
        }

        const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);
        const selectedNames = Array.from(selectedCheckboxes).map(cb => cb.getAttribute('data-name'));

        // Show bulk archive confirmation modal
        const modal = document.getElementById('bulk-archive-modal');
        const message = document.getElementById('bulk-archive-modal-message');
        message.textContent = `Are you sure you want to archive ${selectedIds.length} selected student(s)?`;
        modal.classList.remove('hidden');

        // Handle modal buttons
        const cancelBtn = document.getElementById('cancel-bulk-archive');
        const confirmBtn = document.getElementById('confirm-bulk-archive');

        const closeModal = () => {
          modal.classList.add('hidden');
          cancelBtn.removeEventListener('click', cancelHandler);
          confirmBtn.removeEventListener('click', confirmHandler);
        };

        const cancelHandler = () => closeModal();

        const confirmHandler = () => {
          closeModal();

          // Create form data for bulk archive
          const formData = new FormData();
          formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
          selectedIds.forEach(id => formData.append('student_ids[]', id));

          // Send AJAX request
          fetch('/admin/students/bulk-archive', {
            method: 'POST',
            headers: {
              'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
          })
          .then(response => response.json())
          .then(result => {
            if (result.success) {
              showToast(`Successfully archived ${result.archived_count} student(s).`, 'success');
              // Reload page to reflect changes
              setTimeout(() => {
                window.location.reload();
              }, 1500);
            } else {
              showToast(result.message || 'Failed to archive selected students.', 'error');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred while archiving students.', 'error');
          });
        };

        cancelBtn.addEventListener('click', cancelHandler);
        confirmBtn.addEventListener('click', confirmHandler);
      });
    }

    // Delete selected students logic (for archived students only)
    const deleteSelectedBtn = document.getElementById('delete-selected-btn');
    if (deleteSelectedBtn) {
      deleteSelectedBtn.addEventListener('click', function () {
        const selectedCheckboxes = document.querySelectorAll('.select-archived-student:checked');
        if (selectedCheckboxes.length === 0) {
          showToast('No archived students selected.', 'error');
          return;
        }

        const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);
        const selectedNames = Array.from(selectedCheckboxes).map(cb => cb.getAttribute('data-name'));

        // Show bulk delete confirmation modal
        const modal = document.getElementById('bulk-delete-modal');
        const message = document.getElementById('bulk-delete-modal-message');
        message.textContent = `Are you sure you want to permanently delete ${selectedIds.length} selected archived student(s)? This action cannot be undone.`;
        modal.classList.remove('hidden');

        // Handle modal buttons
        const cancelBtn = document.getElementById('cancel-bulk-delete');
        const confirmBtn = document.getElementById('confirm-bulk-delete');

        const closeModal = () => {
          modal.classList.add('hidden');
          cancelBtn.removeEventListener('click', cancelHandler);
          confirmBtn.removeEventListener('click', confirmHandler);
        };

        const cancelHandler = () => closeModal();

        const confirmHandler = () => {
          closeModal();

          // Create form data for bulk delete
          const formData = new FormData();
          formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
          selectedIds.forEach(id => formData.append('student_ids[]', id));

          // Send AJAX request
          fetch('/admin/students/bulk-delete', {
            method: 'POST',
            headers: {
              'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
          })
          .then(response => response.json())
          .then(result => {
            if (result.success) {
              showToast(`Successfully deleted ${result.deleted_count} archived student(s).`, 'success');
              // Reload page to reflect changes
              setTimeout(() => {
                window.location.reload();
              }, 1500);
            } else {
              showToast(result.message || 'Failed to delete selected archived students.', 'error');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred while deleting archived students.', 'error');
          });
        };

        cancelBtn.addEventListener('click', cancelHandler);
        confirmBtn.addEventListener('click', confirmHandler);
      });
    }
  });
})();

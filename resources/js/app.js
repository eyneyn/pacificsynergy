import 'flowbite';
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    // Sidebar toggle
    const toggleBtn = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('logo-sidebar');
    const content = document.getElementById('main-content');
    const navbar = document.getElementById('top-navbar');
    const sidebarClose = document.getElementById('sidebar-close');
    const hamburgerIcon = document.getElementById('hamburger-icon');
    const welcomeHeader = document.getElementById('welcome-header');

    if (toggleBtn && sidebar && content && navbar && sidebarClose && welcomeHeader) {
        toggleBtn.addEventListener('click', () => {
            const isHidden = sidebar.classList.contains('-translate-x-full');
            if (isHidden) {
                sidebar.classList.remove('-translate-x-full');
                content.classList.add('ml-64');
                navbar.classList.add('ml-64');
                toggleBtn.classList.add('hidden');
            }
        });

        sidebarClose.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            content.classList.remove('ml-64');
            navbar.classList.remove('ml-64');
            toggleBtn.classList.remove('hidden');
            welcomeHeader.classList.add('pl-8');
        });

        // Close sidebar when clicking outside
        document.addEventListener('click', (event) => {
            const isClickInsideSidebar = sidebar.contains(event.target);
            const isClickOnToggleBtn = toggleBtn.contains(event.target);
            const isClickOnCloseBtn = sidebarClose.contains(event.target);
            const isSidebarOpen = !sidebar.classList.contains('-translate-x-full');

            // Close sidebar if click is outside and sidebar is open
            if (!isClickInsideSidebar && !isClickOnToggleBtn && !isClickOnCloseBtn && isSidebarOpen) {
                sidebar.classList.add('-translate-x-full');
                content.classList.remove('ml-64');
                navbar.classList.remove('ml-64');
                toggleBtn.classList.remove('hidden');
                welcomeHeader.classList.add('pl-8');
            }
        });
    }

    // User dropdown
    const userButton = document.getElementById('user-menu-button');
    const dropdown = document.getElementById('user-dropdown');
    const userContainer = document.getElementById('user-menu-container');
    if (userButton && dropdown && userContainer) {
        userButton.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('hidden');
        });
        document.addEventListener('click', (e) => {
            if (!userContainer.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    }

    // 🔍 Auto-submit search while typing (with debounce)
    const searchInput = document.querySelector('input[name="search"]');
    const searchForm = document.getElementById('search-form');
    if (searchInput && searchForm) {
        let debounceTimeout;
        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => {
                // If input is empty, submit to reset list
                if (this.value.trim() === '') {
                    searchForm.submit();
                } else {
                    // Submit search with current query
                    searchForm.submit();
                }
            }, 400); // ⏱ adjust debounce delay here (in milliseconds)
        });
    }

    // Delete confirmation modal
    const confirmationModal = document.getElementById('delete-confirmation-modal');
    const confirmBtn = document.getElementById('confirm-delete-btn');
    const cancelBtn = document.getElementById('cancel-delete-btn');
    const deleteNameSpan = document.getElementById('item-name-to-delete');
    let currentDeleteForm = null;

function setupDeleteHandler(form) {
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const type = form.dataset.deleteType;
        let name = '';
        let id = '';

        switch (type) {
            case 'line':
                id = form.querySelector('#edit_line_id')?.value;
                name = form.querySelector('#edit_line_number')?.value;
                break;
            case 'defect':
                id = form.querySelector('#edit_defect_id')?.value;
                name = form.querySelector('#edit_defect_name')?.value;
                break;
            case 'maintenance':
                id = form.querySelector('#edit_maintenance_id')?.value;
                name = form.querySelector('#edit_maintenance_name')?.value;
                break;
            case 'standard':
                id = form.getAttribute('action')?.split('/').pop();
                name = form.querySelector('#edit_standard_description')?.value;
                break;
        }

        const baseAction = form.dataset.baseAction;
        if (baseAction && id) {
            form.setAttribute('action', baseAction.replace(':id', id));
        }

        if (deleteNameSpan) deleteNameSpan.textContent = `"${name || 'this item'}"`;
        currentDeleteForm = form; // 👈 use the actual form
        confirmationModal?.classList.remove('hidden'); // 👈 now the modal should show
    });
}


    document.querySelectorAll(
        '.delete-line-form, .delete-defect-form, .delete-maintenance-form, .delete-standard-form'
    ).forEach(setupDeleteHandler);

    confirmBtn?.addEventListener('click', () => {
        if (currentDeleteForm) {
            confirmationModal.classList.add('hidden');
            currentDeleteForm.submit();
        }
    });

    cancelBtn?.addEventListener('click', () => {
        confirmationModal.classList.add('hidden');
        currentDeleteForm = null;
    });

    // Update confirmation modal
    const updateModal = document.getElementById('update-confirmation-modal');
    const openUpdateBtn = document.getElementById('open-update-modal-btn');
    const cancelUpdateBtn = document.getElementById('cancel-update-btn');
    const confirmUpdateBtn = document.getElementById('confirm-update-btn');
    const updateForm = document.getElementById('update-report-form');

    if (openUpdateBtn && updateModal && cancelUpdateBtn && confirmUpdateBtn && updateForm) {
        openUpdateBtn.addEventListener('click', () => {
            updateModal.classList.remove('hidden');
        });

        cancelUpdateBtn.addEventListener('click', () => {
            updateModal.classList.add('hidden');
        });

        confirmUpdateBtn.addEventListener('click', () => {
            updateModal.classList.add('hidden');
            updateForm.submit();
        });
    }

    // Validate modal logic
    const openValidateModal = document.getElementById('open-validate-modal');
    const validateModal = document.getElementById('validate-report-modal');
    const cancelValidateBtn = document.getElementById('cancel-validate-btn');

    if (openValidateModal && validateModal && cancelValidateBtn) {
        openValidateModal.addEventListener('click', () => {
            validateModal.classList.remove('hidden');
        });

        cancelValidateBtn.addEventListener('click', () => {
            validateModal.classList.add('hidden');
        });
    }

    // Generic modal open logic for edit modals
    document.querySelectorAll('[data-modal-target]').forEach(row => {
        row.addEventListener('click', () => {
            const target = row.dataset.modalTarget;
            const id = row.dataset.id;

            if (target === 'edit-standard-modal') {
                const form = document.getElementById('edit-standard-form');
                form.action = `/standards/${id}`;
                form.querySelector('[name="description"]').value = row.dataset.description;
                form.querySelector('[name="size"]').value = row.dataset.size;
                form.querySelector('[name="bottles_per_case"]').value = row.dataset.bottles_per_case;
                form.querySelector('[name="mat_no"]').value = row.dataset.mat_no;
                form.querySelector('[name="group"]').value = row.dataset.group;
                form.querySelector('[name="preform_weight"]').value = row.dataset.preform_weight;
                form.querySelector('[name="ldpe_size"]').value = row.dataset.ldpe_size;
                form.querySelector('[name="cases_per_roll"]').value = row.dataset.cases_per_roll;
                form.querySelector('[name="caps"]').value = row.dataset.caps;
                form.querySelector('[name="opp_label"]').value = row.dataset.opp_label;
                form.querySelector('[name="barcode_sticker"]').value = row.dataset.barcode_sticker;
                form.querySelector('[name="alt_preform_for_350ml"]').value = row.dataset.alt_preform_for_350ml;
                form.querySelector('[name="preform_weight2"]').value = row.dataset.preform_weight2;
                document.getElementById('icon-delete-standard-form').action = `/standards/${id}`;
                document.getElementById('edit_standard_description').value = row.dataset.description;
                document.getElementById('edit-standard-modal').classList.remove('hidden');
            }

            if (target === 'edit-defect-modal') {
                const form = document.getElementById('edit-defect-form');
                form.action = `/defect/${id}`;
                form.querySelector('[name="defect_name"]').value = row.dataset.description;
                form.querySelector('[name="category"]').value = row.dataset.size;
                form.querySelector('[name="description"]').value = row.dataset.bottles_per_case;
                document.getElementById('icon-delete-defect-form').action = `/defect/${id}`;
                document.getElementById('edit-defect-id').value = id;
                document.getElementById('edit-defect-modal').classList.remove('hidden');
            }

            if (target === 'edit-maintenance-modal') {
                const form = document.getElementById('edit-maintenance-form');
                form.action = `/maintenance/${id}`;
                form.querySelector('[name="name"]').value = row.dataset.description;
                form.querySelector('[name="type"]').value = row.dataset.size;
                document.getElementById('icon-delete-maintenance-form').action = `/maintenance/${id}`;
                document.getElementById('edit-maintenance-modal').classList.remove('hidden');
            }

            if (target === 'edit-line-modal') {
                const form = document.getElementById('edit-line-form');
                form.action = `/configuration/index/${id}`;
                document.getElementById('edit_line_id').value = id;
                document.getElementById('edit_line_number').value = row.dataset.line_number;
                document.getElementById('edit_status').value = row.dataset.status;
                document.getElementById('icon-delete-line-form').action = `/configuration/index/${id}`;
                if (typeof deleteNameSpan !== 'undefined') {
                    deleteNameSpan.textContent = `Line ${row.dataset.line_number}`;
                }
                currentDeleteForm = document.getElementById('icon-delete-line-form');
                document.getElementById('edit-line-modal').classList.remove('hidden');
            }
        });
    });

document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('btnNoReport');
  if (!btn) return;

  btn.addEventListener('click', function (e) {
    const dateEl = document.querySelector('input[name="production_date"]');
    // allow select or hidden input produced by x-select-dropdown
    const lineEl = document.querySelector('select[name="line"], input[name="line"]');

    const date = (dateEl?.value || '').trim();
    const line = (lineEl?.value || '').toString().trim();

    if (!date || !line) {
      e.preventDefault();
      alert('Please select both Production Date and Line.');
    }
  });
});
});
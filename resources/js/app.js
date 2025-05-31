import 'flowbite';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('sidebar-toggle');
    const closeBtn = document.getElementById('sidebar-close');
    const sidebar = document.getElementById('logo-sidebar');

    if (toggleBtn && closeBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            toggleBtn.classList.add('hidden');
            closeBtn.classList.remove('hidden');
        });

        closeBtn.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            toggleBtn.classList.remove('hidden');
            closeBtn.classList.add('hidden');
        });
    }
});

    const userButton = document.getElementById('user-menu-button');
    const dropdown = document.getElementById('user-dropdown');

    // Toggle dropdown visibility on button click
    userButton.addEventListener('click', function (e) {
        e.stopPropagation();
        dropdown.classList.toggle('hidden');
    });

    // Close dropdown if clicked outside
    document.addEventListener('click', function (e) {
        if (!document.getElementById('user-menu-container').contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });

const confirmationModal = document.getElementById('delete-confirmation-modal');
const confirmBtn = document.getElementById('confirm-delete-btn');
const cancelBtn = document.getElementById('cancel-delete-btn');
const deleteNameSpan = document.getElementById('item-name-to-delete');

let currentDeleteForm = null;

// 🔧 Function to handle delete form confirmation modals
function setupDeleteHandler(formId) {
    const form = document.getElementById(formId);
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const type = form.dataset.deleteType;
        let nameInput, idInput;

        switch (type) {
            case 'report':
                nameInput = document.getElementById('edit_report_date');
                break;
            case 'maintenance':
                nameInput = document.getElementById('edit_maintenance_name');
                break;
            case 'defect':
                nameInput = document.getElementById('edit_defect_name');
                break;
            case 'standard':
                nameInput = document.getElementById('edit_standard_description');
                break;
            case 'line':
                nameInput = document.getElementById('edit_line_number');
                idInput = document.getElementById('edit_line_id');
                
                // Replace action URL placeholder with actual line ID
                const baseAction = form.getAttribute('data-base-action');
                const actualId = idInput?.value;
                form.setAttribute('action', baseAction.replace(':id', actualId));
                break;
        }

        const displayName = nameInput?.value || 'this item';
        deleteNameSpan.textContent = `"${displayName}"`;

        currentDeleteForm = form;
        confirmationModal.classList.remove('hidden');
    });
}


// 📦 Register all supported delete forms
setupDeleteHandler('icon-delete-maintenance-form');
setupDeleteHandler('icon-delete-defect-form');
setupDeleteHandler('icon-delete-standard-form');
setupDeleteHandler('icon-delete-line-form');
setupDeleteHandler('icon-delete-report-form');

// ✅ Confirm delete
confirmBtn.addEventListener('click', () => {
    if (currentDeleteForm) {
        confirmationModal.classList.add('hidden');
        currentDeleteForm.submit();
    }
});

// ❌ Cancel delete
cancelBtn.addEventListener('click', () => {
    confirmationModal.classList.add('hidden');
    currentDeleteForm = null;
});

// 🔁 OPEN MODALS by target type (for defect, standard, maintenance)
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
            const lineNumber = row.dataset.line_number;
            const status = row.dataset.status;

            const form = document.getElementById('edit-line-form');
            form.action = `/configuration/${id}`;
            document.getElementById('edit_line_id').value = id;
            document.getElementById('edit_line_number').value = lineNumber;
            document.getElementById('edit_status').value = status;

            document.getElementById('icon-delete-line-form').action = `/configuration/${id}`;
            deleteNameSpan.textContent = `Line ${lineNumber}`;
            currentDeleteForm = document.getElementById('icon-delete-line-form');

            document.getElementById('edit-line-modal').classList.remove('hidden');
        }
    });
});

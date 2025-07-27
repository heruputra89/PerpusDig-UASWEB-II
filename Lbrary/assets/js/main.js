// Sidebar Toggle
const sidebarToggle = document.getElementById('sidebarToggle');
if (sidebarToggle) {
    sidebarToggle.addEventListener('click', () => {
        document.body.classList.toggle('sidebar-collapsed');
    });
}

// Form Validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return;

    form.addEventListener('submit', (e) => {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
    });
}

// File Upload Preview
function previewImage(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);

    if (input && preview) {
        input.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }
}

// Date Picker
function initializeDatePicker(inputId) {
    const input = document.getElementById(inputId);
    if (input) {
        input.type = 'date';
        input.min = new Date().toISOString().split('T')[0];
    }
}

// Auto-save
function autoSave(formId, saveUrl) {
    const form = document.getElementById(formId);
    if (!form) return;

    let timeout;
    form.addEventListener('input', () => {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            const formData = new FormData(form);
            fetch(saveUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Data berhasil disimpan', 'success');
                } else {
                    showToast('Gagal menyimpan data', 'error');
                }
            })
            .catch(error => {
                showToast('Terjadi kesalahan', 'error');
            });
        }, 1000);
    });
}

// Toast Notification
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
        <div class="toast-header">
            <strong class="me-auto">${type.charAt(0).toUpperCase() + type.slice(1)}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">
            ${message}
        </div>
    `;

    document.body.appendChild(toast);
    const toastInstance = new bootstrap.Toast(toast);
    toastInstance.show();

    setTimeout(() => {
        toastInstance.hide();
        toast.remove();
    }, 5000);
}

// Loading Spinner
function showLoading() {
    document.querySelector('.loading-spinner').classList.add('active');
}

function hideLoading() {
    document.querySelector('.loading-spinner').classList.remove('active');
}

// Table Search
function searchTable(tableId, inputId) {
    const table = document.getElementById(tableId);
    const input = document.getElementById(inputId);

    if (table && input) {
        input.addEventListener('input', () => {
            const filter = input.value.toLowerCase();
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let found = false;

                for (let j = 0; j < cells.length; j++) {
                    const cell = cells[j];
                    if (cell) {
                        const text = cell.textContent || cell.innerText;
                        if (text.toLowerCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }

                rows[i].style.display = found ? '' : 'none';
            }
        });
    }
}

// Initialize all components
function initializeComponents() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Initialize modals
    const modalTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="modal"]'));
    modalTriggerList.map(function(modalTriggerEl) {
        return new bootstrap.Modal(modalTriggerEl);
    });

    // Initialize AOS
    AOS.init({
        duration: 1000,
        once: true
    });
}

// Initialize all components on DOM ready
document.addEventListener('DOMContentLoaded', initializeComponents);

// Handle form submissions with AJAX
function submitFormAjax(formId, successCallback, errorCallback) {
    const form = document.getElementById(formId);
    if (!form) return;

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const formData = new FormData(form);
        const url = form.action;

        showLoading();

        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showToast(data.message || 'Berhasil!', 'success');
                if (typeof successCallback === 'function') {
                    successCallback(data);
                }
            } else {
                showToast(data.message || 'Gagal!', 'error');
                if (typeof errorCallback === 'function') {
                    errorCallback(data);
                }
            }
        })
        .catch(error => {
            hideLoading();
            showToast('Terjadi kesalahan', 'error');
            if (typeof errorCallback === 'function') {
                errorCallback(error);
            }
        });
    });
}

// Confirm Dialog
function confirmAction(message, successCallback, errorCallback) {
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = 'confirmModal';
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    ${message}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="confirmBtn">Ya, Lanjutkan</button>
                </div>
            </div>
        </div>
    `;

    document.body.appendChild(modal);
    const modalInstance = new bootstrap.Modal(modal);
    modalInstance.show();

    const confirmBtn = document.getElementById('confirmBtn');
    confirmBtn.addEventListener('click', () => {
        modalInstance.hide();
        if (typeof successCallback === 'function') {
            successCallback();
        }
    });

    modal.addEventListener('hidden.bs.modal', () => {
        if (typeof errorCallback === 'function') {
            errorCallback();
        }
        modal.remove();
    });
}

// Image Cropper
function initializeImageCropper(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);

    if (input && preview) {
        input.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const image = new Image();
                    image.src = e.target.result;
                    image.onload = () => {
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');
                        
                        // Calculate dimensions
                        const maxWidth = 400;
                        const maxHeight = 400;
                        let width = image.width;
                        let height = image.height;

                        if (width > height) {
                            if (width > maxWidth) {
                                height *= maxWidth / width;
                                width = maxWidth;
                            }
                        } else {
                            if (height > maxHeight) {
                                width *= maxHeight / height;
                                height = maxHeight;
                            }
                        }

                        canvas.width = width;
                        canvas.height = height;
                        ctx.drawImage(image, 0, 0, width, height);

                        preview.src = canvas.toDataURL();
                    };
                };
                reader.readAsDataURL(file);
            }
        });
    }
}

// Table Sort
function sortTable(tableId, columnIndex, dataType = 'text') {
    const table = document.getElementById(tableId);
    if (!table) return;

    const tbody = table.getElementsByTagName('tbody')[0];
    const rows = Array.from(tbody.getElementsByTagName('tr'));

    rows.sort((a, b) => {
        const aVal = a.getElementsByTagName('td')[columnIndex].textContent;
        const bVal = b.getElementsByTagName('td')[columnIndex].textContent;

        if (dataType === 'number') {
            return parseFloat(aVal) - parseFloat(bVal);
        } else if (dataType === 'date') {
            return new Date(aVal) - new Date(bVal);
        }
        return aVal.localeCompare(bVal);
    });

    tbody.innerHTML = '';
    rows.forEach(row => tbody.appendChild(row));
}

// Pagination
function initializePagination(totalItems, itemsPerPage, currentPage, onPageChange) {
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    const pagination = document.createElement('nav');
    pagination.className = 'pagination-container';
    
    const ul = document.createElement('ul');
    ul.className = 'pagination';

    // Previous button
    const prevLi = document.createElement('li');
    prevLi.className = 'page-item';
    const prevA = document.createElement('a');
    prevA.className = 'page-link';
    prevA.href = '#';
    prevA.textContent = 'Previous';
    prevA.onclick = () => {
        if (currentPage > 1) {
            onPageChange(currentPage - 1);
        }
    };
    prevLi.appendChild(prevA);
    ul.appendChild(prevLi);

    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        const li = document.createElement('li');
        li.className = `page-item ${i === currentPage ? 'active' : ''}`;
        const a = document.createElement('a');
        a.className = 'page-link';
        a.href = '#';
        a.textContent = i;
        a.onclick = () => onPageChange(i);
        li.appendChild(a);
        ul.appendChild(li);
    }

    // Next button
    const nextLi = document.createElement('li');
    nextLi.className = 'page-item';
    const nextA = document.createElement('a');
    nextA.className = 'page-link';
    nextA.href = '#';
    nextA.textContent = 'Next';
    nextA.onclick = () => {
        if (currentPage < totalPages) {
            onPageChange(currentPage + 1);
        }
    };
    nextLi.appendChild(nextA);
    ul.appendChild(nextLi);

    pagination.appendChild(ul);
    return pagination;
}

// Initialize all form validations
document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', (event) => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
});

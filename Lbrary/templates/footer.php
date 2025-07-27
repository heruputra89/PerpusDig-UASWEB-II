        </div>
    </div>

    <footer class="bg-light mt-5 py-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-3">E-Perpustakaan</h5>
                    <p class="text-muted">Sistem Informasi Perpustakaan Digital</p>
                </div>
                <div class="col-md-3">
                    <h5 class="mb-3">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="buku.php" class="text-muted text-decoration-none">Daftar Buku</a></li>
                        <li><a href="kategori.php" class="text-muted text-decoration-none">Kategori</a></li>
                        <li><a href="profile.php" class="text-muted text-decoration-none">Profil</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5 class="mb-3">Contact</h5>
                    <p class="text-muted">
                        <i class="bi bi-telephone me-2"></i>+62 812-3456-7890<br>
                        <i class="bi bi-envelope me-2"></i>info@eperpustakaan.com
                    </p>
                </div>
            </div>
            <hr class="my-4">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="text-muted">&copy; <?php echo date('Y'); ?> E-Perpustakaan. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5.3 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/main.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true
        });

        // Sidebar toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            
            if (sidebar.classList.contains('collapsed')) {
                sidebar.classList.remove('collapsed');
                mainContent.style.marginLeft = '250px';
            } else {
                sidebar.classList.add('collapsed');
                mainContent.style.marginLeft = '70px';
            }
        }

        // Toast notification
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

        // Loading spinner
        function showLoading() {
            document.querySelector('.loading-spinner').classList.add('active');
        }

        function hideLoading() {
            document.querySelector('.loading-spinner').classList.remove('active');
        }

        // Form validation
        function validateForm(formId) {
            const form = document.getElementById(formId);
            if (!form.checkValidity()) {
                form.reportValidity();
                return false;
            }
            return true;
        }

        // Table search
        function searchTable(tableId, inputId) {
            const table = document.getElementById(tableId);
            const input = document.getElementById(inputId);
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
        }

        // File upload preview
        function previewImage(input, previewId) {
            const file = input.files[0];
            const preview = document.getElementById(previewId);

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }

        // Date picker
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
            form.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(function() {
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

        // Initialize all components
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>
</body>
</html>

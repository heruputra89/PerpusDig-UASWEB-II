<?php
include 'config/session_check.php';
include 'config/controller.php';

/// Check if user is a admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success = $error = '';

// Get admin's profile
$query = "SELECT * FROM user WHERE id_user = '$user_id'";
$profile = select($query)[0];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $no_telp = $_POST['no_telp'];
    
    // Handle file upload
    $foto = $profile['foto']; // Keep existing photo by default
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/profile/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            $new_filename = uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $upload_path)) {
                // Delete old photo if exists
                if (!empty($profile['foto']) && file_exists($upload_dir . $profile['foto'])) {
                    unlink($upload_dir . $profile['foto']);
                }
                $foto = $new_filename;
            } else {
                $error = "Gagal mengupload foto.";
            }
        } else {
            $error = "Format file tidak didukung. Gunakan JPG, JPEG, atau PNG.";
        }
    }
    
    if (empty($error)) {
        $query = "UPDATE user SET 
                  name = '$name',
                  username = '$username',
                  email = '$email',
                  no_telp = '$no_telp',
                  foto = '$foto'
                  WHERE id_user = '$user_id'";
        
        if (mysqli_query($conn, $query)) {
            $success = "Profil berhasil diperbarui!";
            // Refresh profile data
            $profile = select("SELECT * FROM user WHERE id_user = '$user_id'")[0];
            echo "<script>
                alert('Profil berhasil diperbarui!');
                window.location.href = 'dashboard_admin.php';
            </script>";
        } else {
            $error = "Gagal memperbarui profil: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id" class="scroll-smooth" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil Admin - Lbrary</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <style>
        :root {
            /* Modern Dark Theme */
            --bg-primary: #0A0A0A;
            --bg-secondary: #1A1A1A;
            --text-primary: #FFFFFF;
            --text-secondary: #A3A3A3;
            --accent-primary: #FFD700;
            --accent-secondary: #FFF8DC;
            --border-color: rgba(255, 255, 255, 0.1);
            --card-bg: rgba(255, 255, 255, 0.05);
            --nav-bg: rgba(10, 10, 10, 0.8);
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
            --glass-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }

        [data-theme="light"] {
            --bg-primary: #FFFFFF;
            --bg-secondary: #F3F4F6;
            --text-primary: #1F2937;
            --text-secondary: #4B5563;
            --accent-primary: #FFD700;
            --accent-secondary: #FFF8DC;
            --border-color: rgba(0, 0, 0, 0.1);
            --card-bg: #FFFFFF;
            --nav-bg: rgba(255, 255, 255, 0.8);
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.2);
            --glass-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.1);
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
        }

        .navbar {
            background: var(--nav-bg) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
        }

        .navbar-brand {
            color: var(--text-primary) !important;
        }

        .nav-link {
            color: var(--text-secondary) !important;
        }

        .nav-link:hover {
            color: var(--text-primary) !important;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            box-shadow: var(--glass-shadow);
        }

        .form-control {
            background: var(--glass-bg);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
        }

        .form-control:focus {
            background: var(--glass-bg);
            border-color: var(--accent-primary);
            color: var(--text-primary);
            box-shadow: 0 0 0 0.25rem rgba(34, 197, 94, 0.25);
        }

        .form-label {
            color: var(--text-secondary);
        }

        .btn-primary {
            background: var(--accent-primary);
            border: none;
            color: black;
        }

        .btn-primary:hover {
            background: var(--accent-secondary);
            color: black;
        }

        .btn-secondary {
            background: var(--glass-bg);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
        }

        .btn-secondary:hover {
            background: var(--glass-bg);
            border-color: var(--accent-primary);
            color: var(--accent-primary);
        }

        .profile-img, .profile-img-preview {
            border: 5px solid var(--glass-bg);
            box-shadow: var(--glass-shadow);
        }

        .modal-content {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
        }

        .modal-header {
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
        }

        .modal-footer {
            border-top: 1px solid var(--border-color);
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            color: var(--accent-primary);
            border: 1px solid var(--accent-primary);
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid #ef4444;
        }

        .dropdown-menu {
            min-width: 200px;
            padding: 0.5rem;
            margin-top: 0.5rem;
        }

        .dropdown-item {
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
        }

        .dropdown-item:hover {
            background: var(--glass-bg);
        }

        .dropdown-divider {
            margin: 0.5rem 0;
            border-color: var(--border-color);
        }

        /* Theme Toggle */
        .theme-toggle {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: var(--accent-primary);
            color: white;
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 4px 15px rgba(34, 197, 94, 0.3);
            transition: all 0.3s ease;
            z-index: 50;
            cursor: pointer;
            border: none;
            outline: none;
        }

        .theme-toggle:hover {
            background: var(--accent-secondary);
            transform: translateY(-3px) rotate(180deg);
        }
    </style>
</head>
<body>
    <!-- Modern Navbar -->
    <nav class="navbar glass">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="ri-book-2-line"></i>
                Lbrary
            </a>
            <!-- Mobile Menu Button -->
            <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="ri-menu-line"></i>
            </button>
            <!-- Desktop Profile -->
            <div class="d-none d-md-flex align-items-center ms-auto">
                <div class="dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <img src="<?= !empty($profile['foto']) ? 'uploads/profile/' . $profile['foto'] : 'assets/img/default-avatar.png' ?>" 
                             alt="Profile" class="rounded-circle" width="35" height="35" style="object-fit: cover;">
                        <span class="d-none d-lg-inline"><?= htmlspecialchars($profile['name']) ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="edit_profile_admin.php"><i class="ri-user-settings-line me-2"></i>Edit Profil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="ri-logout-box-line me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
            <!-- Mobile Profile (Collapsible) -->
            <div class="collapse navbar-collapse d-md-none" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="userDropdownMobile" role="button" data-bs-toggle="dropdown">
                            <img src="<?= !empty($profile['foto']) ? 'uploads/profile/' . $profile['foto'] : 'assets/img/default-avatar.png' ?>" 
                                 alt="Profile" class="rounded-circle" width="35" height="35" style="object-fit: cover;">
                            <?= htmlspecialchars($profile['name']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="index.php"><i class="ri-home-line me-2"></i>Home</a></li>
                            <li><a class="dropdown-item" href="buku.php"><i class="ri-book-2-line me-2"></i>Katalog Buku</a></li>
                            <li><a class="dropdown-item" href="dashboard_admin.php"><i class="ri-dashboard-line me-2"></i>Dashboard</a></li>
                            <li><a class="dropdown-item" href="edit_profile_admin.php"><i class="ri-user-settings-line me-2"></i>Edit Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="ri-logout-box-line me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary">Edit Profil</h2>
            <a href="dashboard_admin.php" class="btn btn-secondary">
                <i class="ri-arrow-left-line me-2"></i>Kembali
            </a>
        </div>

        <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $success ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-4 text-center mb-4">
                            <img src="<?= !empty($profile['foto']) ? 'uploads/profile/' . $profile['foto'] : 'assets/img/default-avatar.png' ?>" 
                                 alt="Current Profile" class="profile-img mb-3" id="currentPhoto">
                            <img src="" alt="Preview" class="profile-img-preview mb-3" id="photoPreview">
                            <div class="mb-3">
                                <label for="foto" class="form-label">Ubah Foto Profil</label>
                                <input type="file" class="form-control" id="foto" name="foto" accept="image/*" onchange="openCropper(this)">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($profile['name'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($profile['username'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($profile['email'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label for="no_telp" class="form-label">Nomor Telepon</label>
                                <input type="text" class="form-control" id="no_telp" name="no_telp" value="<?= htmlspecialchars($profile['no_telp'] ?? '') ?>">
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check me-2"></i>Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Theme Toggle Button -->
    <!-- <button class="theme-toggle" id="themeToggle">
        <i class="bi bi-moon"></i>
    </button> -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    
    <!-- Cropper Modal -->
    <div class="modal fade" id="cropperModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Posisikan Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <img id="cropperImage" src="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="cropImage()">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Theme Toggle
        const themeToggle = document.getElementById('themeToggle');
        const html = document.documentElement;
        const icon = themeToggle.querySelector('i');

        function setTheme(theme) {
            html.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            icon.className = theme === 'light' ? 'bi bi-sun' : 'bi bi-moon';
        }

        function loadTheme() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme) {
                setTheme(savedTheme);
            } else {
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                setTheme(prefersDark ? 'dark' : 'light');
            }
        }

        themeToggle.addEventListener('click', () => {
            const currentTheme = html.getAttribute('data-theme');
            setTheme(currentTheme === 'light' ? 'dark' : 'light');
        });

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            if (!localStorage.getItem('theme')) {
                setTheme(e.matches ? 'dark' : 'light');
            }
        });

        loadTheme();

        // Existing Cropper Code
        let cropper;
        let originalFile;

        function openCropper(input) {
            if (input.files && input.files[0]) {
                originalFile = input.files[0];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const modal = new bootstrap.Modal(document.getElementById('cropperModal'));
                    const image = document.getElementById('cropperImage');
                    image.src = e.target.result;
                    
                    modal.show();
                    
                    image.onload = function() {
                        if (cropper) {
                            cropper.destroy();
                        }
                        
                        cropper = new Cropper(image, {
                            aspectRatio: 1,
                            viewMode: 1,
                            dragMode: 'move',
                            autoCropArea: 1,
                            restore: false,
                            guides: true,
                            center: true,
                            highlight: false,
                            cropBoxMovable: true,
                            cropBoxResizable: true,
                            toggleDragModeOnDblclick: false,
                            ready: function() {
                                const containerData = cropper.getContainerData();
                                const cropBoxData = cropper.getCropBoxData();
                                const left = (containerData.width - cropBoxData.width) / 2;
                                const top = (containerData.height - cropBoxData.height) / 2;
                                cropper.setCropBoxData({ left, top });
                            }
                        });
                    };
                }
                
                reader.readAsDataURL(originalFile);
            }
        }

        function cropImage() {
            if (!cropper) return;
            
            const canvas = cropper.getCroppedCanvas({
                width: 300,
                height: 300
            });
            
            canvas.toBlob((blob) => {
                const preview = document.getElementById('photoPreview');
                const current = document.getElementById('currentPhoto');
                
                preview.src = canvas.toDataURL();
                preview.style.display = 'block';
                current.style.display = 'none';
                
                const croppedFile = new File([blob], originalFile.name, {
                    type: 'image/jpeg',
                    lastModified: new Date().getTime()
                });
                
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(croppedFile);
                
                const fileInput = document.getElementById('foto');
                fileInput.files = dataTransfer.files;
                
                bootstrap.Modal.getInstance(document.getElementById('cropperModal')).hide();
            }, 'image/jpeg', 0.9);
        }
    </script>
</body>
</html> 
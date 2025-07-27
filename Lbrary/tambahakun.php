<?php
include "config/controller.php";
include "config/session_check.php";

// Check if user is admin
checkRole('admin');

// Fungsi tambah_akun

if (isset($_POST['tambah'])) {
    if (tambah_akun($_POST) > 0) {
        echo "<script>
        alert('Data berhasil ditambahkan');
        document.location.href='" . getDashboardUrl() . "';
        </script>";
    } else {
        echo "<script>
        alert('Data gagal ditambahkan');
        document.location.href='" . getDashboardUrl() . "';
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Akun - Lbrary</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
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
            --accent-orange: #D97706;
            --accent-blue: #29C5F6;
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
            min-height: 100vh;
            padding: 2rem;
        }

        .navbar {
            background: var(--nav-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border-color);
        }

        .form-container {
            max-width: 800px;
            margin: 2rem auto;
        }

        .form-card {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: var(--glass-shadow);
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .form-subtitle {
            color: var(--text-secondary);
            font-size: 1rem;
        }

        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
         
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.2);
            outline: none;
   
        }

        .form-label {
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 0.75rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--accent-primary);
            color: black;
         
        }

        .btn-primary:hover {
            background: var(--accent-blue);
            transform: translateY(-2px);
      
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }

        .back-link {
            color: var(--text-primary);
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            margin-bottom: 1rem;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            background: rgba(255, 255, 255, 0.05);
        }

        .back-link:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(-5px);
        }

        select option {
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        .alert {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .alert-success {
            border-color: var(--accent-primary);
            color: var(--accent-primary);
        }

        .alert-danger {
            border-color: #ef4444;
            color: #ef4444;
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="form-container" data-aos="fade-up">
        <a href="<?= getDashboardUrl() ?>" class="back-link" data-aos="fade-right">
            <i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard
        </a>

        <div class="form-card">
            <div class="form-header">
                <h1 class="form-title">
                    <i class="bi bi-person-plus me-2"></i>Tambah Akun Baru
                </h1>
                <hr><br>
            </div>

            <form action="" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Masukkan nama lengkap" required>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" required>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email" required>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="password-container">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
                            <button type="button" class="toggle-password" onclick="togglePassword()">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="no_telp" class="form-label">No. HP</label>
                        <input type="number" class="form-control" id="no_telp" name="no_telp" placeholder="Masukkan No. HP" maxlength="13">
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="level" class="form-label">Role</label>
                        <select class="form-select" name="level" id="level" required>
                            <option value="" selected disabled>Pilih role</option>
                            <option value="petugas">Petugas</option>
                            <option value="user">User</option>
                            
                            
                        </select>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" name="status" id="status" required>
                            <option value="" selected disabled>Pilih status</option>
                            <option value="aktif">Aktif</option>
                            <option value="tidakAktif">Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" name="tambah" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Simpan
                    </button>
                    <a href="<?= getDashboardUrl() ?>" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-2"></i>Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true
        });

        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.querySelector('.toggle-password i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleButton.classList.remove('bi-eye');
                toggleButton.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleButton.classList.remove('bi-eye-slash');
                toggleButton.classList.add('bi-eye');
            }
        }
    </script>
</body>
</html>
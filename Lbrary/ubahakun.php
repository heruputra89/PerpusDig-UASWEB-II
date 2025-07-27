<?php
include("config/controller.php");
include("config/session_check.php");

// Check if user is an admin
checkRole('admin');

$success = $error = '';

// Mengambil id data akun
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $akun = select("SELECT * FROM user WHERE id_user=$id")[0];

    if (!$akun) {
        header("Location: " . getDashboardUrl());
        exit();
    }

    if (isset($_POST['ubah'])) {
        $name = trim($_POST['name']);
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $level = $_POST['level'];
        $status = $_POST['status'];
        $email = trim($_POST['email']);
        $no_telp = trim($_POST['no_telp']);

        // Validation
        if (empty($name) || empty($username)) {
            $error = "Nama dan username tidak boleh kosong!";
        } else {
            // Check if username already exists (excluding current user)
            $check_username = select("SELECT * FROM user WHERE username = '$username' AND id_user != $id");
            if (!empty($check_username)) {
                $error = "Username sudah digunakan!";
            } else {
                // Update user data
                $query = "UPDATE user SET 
                         name = '$name',
                         username = '$username',
                         
                         level = '$level',
                         status = '$status',
                         email = '$email',
                         no_telp = '$no_telp'";
                
                // Only update password if it's not empty
                if (!empty($password)) {
                    $query .= ", password = '$password'";
                }
                
                $query .= " WHERE id_user = $id";

                if (mysqli_query($conn, $query)) {
                    $success = "Data berhasil diperbarui!";
                    // Refresh user data
                    $akun = select("SELECT * FROM user WHERE id_user=$id")[0];
                    
                } else {
                    $error = "Gagal memperbarui data: " . mysqli_error($conn);
                }
            }
        }
    }
} else {
    header("Location: adminPage.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id" class="scroll-smooth" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Akun - Lbrary</title>
    <link rel="icon" href="https://images.rawpixel.com/image_png_800/czNmcy1wcml2YXRlL3Jhd3BpeGVsX2ltYWdlcy93ZWJzaXRlX2NvbnRlbnQvbHIvam9iNjgyLTI0NS1wLnBuZw.png" type="image/x-icon">
    <!-- Modern Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <!-- Tailwind CSS with Modern Config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        },
                        dark: {
                            100: '#1E1E1E',
                            200: '#2D2D2D',
                            300: '#3C3C3C',
                            400: '#4B4B4B',
                            500: '#5A5A5A',
                        }
                    },
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <!-- Modern Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            background: rgba(255, 255, 255, 0.1);
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
    </style>
</head>
<body>
    

    <div class="form-container">
        <a href="<?= getDashboardUrl() ?>#pengguna-section" class="back-link">
            <i class="ri-arrow-left-line mr-2"></i>Kembali ke Dashboard
        </a>

        <div class="form-card">
            <div class="form-header">
                <h1 class="form-title">
                    <i class="ri-user-settings-line mr-2"></i>Ubah Data Pengguna
                </h1>
                <hr>
                <br>
            </div>

            <?php if ($success): ?>
            <div class="alert alert-success">
                <?= $success ?>
            </div>
            <?php endif; ?>

            <?php if ($error): ?>
            <div class="alert alert-danger">
                <?= $error ?>
            </div>
            <?php endif; ?>

            <form action="" method="POST">
                <input type="hidden" name="id" value="<?= $akun['id_user']; ?>">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control w-full" id="name" name="name" value="<?= htmlspecialchars($akun['name']) ?>" required>
                    </div>

                    <div>
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control w-full" id="username" name="username" value="<?= htmlspecialchars($akun['username']) ?>" required>
                    </div>

                    <div>
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control w-full" id="password" name="password" value="<?= htmlspecialchars($akun['password']) ?>" disabled>
                    </div>

                    <div>
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control w-full" id="email" name="email" value="<?= htmlspecialchars($akun['email'] ?? '') ?>">
                    </div>

                    <div>
                        <label for="level" class="form-label">Level</label>
                        <select class="form-select w-full" id="level" name="level" required>
                        <option value="petugas" <?= $akun['level'] === 'petugas' ? 'selected' : '' ?>>Petugas</option>
                            <option value="user" <?= $akun['level'] === 'user' ? 'selected' : '' ?>>User</option>
                            
                         
                        </select>
                    </div>

                    <div>
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select w-full" id="status" name="status" required>
                            <option value="aktif" <?= $akun['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                            <option value="nonaktif" <?= $akun['status'] === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                        </select>
                    </div>

                    <div>
                        <label for="no_telp" class="form-label">No. Telepon</label>
                        <input type="tel" class="form-control w-full" id="no_telp" name="no_telp" value="<?= htmlspecialchars($akun['no_telp'] ?? '') ?>" maxlength="13">
                    </div>
                </div>

                <div class="flex gap-4 mt-8">
                    <button type="submit" name="ubah" class="btn btn-primary">
                        <i class="ri-check-line"></i>
                        Simpan Perubahan
                    </button>
                    <a href="<?= getDashboardUrl() ?>#pengguna-section" class="btn btn-secondary">
                        <i class="ri-close-line"></i>
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
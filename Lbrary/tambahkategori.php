<?php
include "config/controller.php";
include "config/session_check.php";

// Check if user is admin or petugas
$role = getCurrentRole();
if (!in_array($role, ['admin', 'petugas'])) {
    header("Location: " . getDashboardUrl());
    exit();
}

// Fungsi tambah_kategori

if (isset($_POST['tambah'])) {
    if (tambah_kategori($_POST) > 0) {
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
<html lang="id" class="scroll-smooth" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kategori | Lbrary</title>
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

        .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .form-control:focus {
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
            color: black;
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
    </style>
</head>
<body>
    <div class="form-container">
        <a href="<?= getDashboardUrl() ?>#kategori-section" class="back-link">
            <i class="ri-arrow-left-line mr-2"></i>Kembali ke Dashboard
        </a>

        <div class="form-card">
            <div class="form-header">
                <h1 class="form-title">
                    <i class="ri-price-tag-3-line mr-2"></i>Tambah Kategori
                </h1>
                <p class="form-subtitle">Tambahkan kategori baru untuk pengelompokan buku</p>
            </div>

            <form action="" method="POST">
                <div class="mb-6">
                    <label for="nama" class="form-label">Nama Kategori</label>
                    <input type="text" class="form-control w-full" id="nama" name="nama" placeholder="Masukkan nama kategori" required>
                </div>

                <div class="flex gap-4" id="myForm">
                    <button type="submit" name="tambah" class="btn btn-primary">
                        <i class="ri-check-line"></i>
                        Simpan
                    </button>
                    <a href="<?= getDashboardUrl() ?>#kategori-section" class="btn btn-secondary">
                        <i class="ri-close-line"></i>
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        <?php if (isset($_POST['tambah'])): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Data berhasil ditambahkan',
        showConfirmButton: false,
        timer: 2000,
        background: '#1A1A1A',
        color: '#FFFFFF'
    }).then(() => {
        window.location.href = '<?= getDashboardUrl(); ?>';
    });
</script>
<?php endif; ?>

</script>
</body>
</html>
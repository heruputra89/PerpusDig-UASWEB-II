<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title) : 'E-Perpustakaan'; ?></title>
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
        }

        .sidebar {
            background-color: var(--bg-secondary);
            color: var(--text-primary);
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar .nav-link {
            color: var(--text-secondary);
            padding: 10px 20px;
            border-radius: 5px;
            margin: 5px 15px;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover {
            color: var(--text-primary);
            background-color: var(--glass-bg);
        }

        .sidebar .nav-link.active {
            color: var(--text-primary);
            background-color: var(--accent-primary);
        }

        .main-content {
            margin-left: 250px;
            transition: all 0.3s ease;
        }

        .sidebar.collapsed + .main-content {
            margin-left: 70px;
        }

        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            box-shadow: var(--glass-shadow);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .stat-card {
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            color: var(--text-primary);
        }

        .stat-card .stat-value {
            font-size: 24px;
            font-weight: bold;
        }

        .table {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
        }

        .table th {
            background-color: var(--light-color);
            color: var(--dark-color);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.1);
        }

        .btn {
            border-radius: 5px;
            padding: 8px 20px;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
        }

        .loading-spinner {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1050;
        }

        .loading-spinner.active {
            display: block;
        }

        .loading-spinner .spinner-border {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 50px;
            height: 50px;
        }
    </style>
</head>
<body>
    <div class="loading-spinner">
        <div class="spinner-border text-light" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header p-3">
            <div class="d-flex align-items-center">
                <button class="btn btn-link text-white d-md-none" onclick="toggleSidebar()">
                    <i class="bi bi-list"></i>
                </button>
                <div class="d-flex align-items-center">
                    <img src="assets/images/logo.png" alt="Logo" class="img-fluid" style="width: 40px; height: 40px;">
                    <div class="ms-2">
                        <h5 class="mb-0">E-Perpustakaan</h5>
                        <small class="text-muted">Sistem Informasi Perpustakaan</small>
                    </div>
                </div>
            </div>
        </div>

        <nav class="nav flex-column mt-4">
            <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>">
                <i class="bi bi-house-door me-2"></i> Dashboard
            </a>
            <?php if($_SESSION['role'] === 'admin'): ?>
                <a href="adminPage.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'adminPage.php' ? 'active' : ''; ?>">
                    <i class="bi bi-gear me-2"></i> Admin Panel
                </a>
            <?php endif; ?>
            <a href="buku.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'buku.php' ? 'active' : ''; ?>">
                <i class="bi bi-book me-2"></i> Daftar Buku
            </a>
            <a href="kategori.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'kategori.php' ? 'active' : ''; ?>">
                <i class="bi bi-tags me-2"></i> Kategori
            </a>
            <?php if($_SESSION['role'] !== 'siswa'): ?>
                <a href="anggota.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'anggota.php' ? 'active' : ''; ?>">
                    <i class="bi bi-people me-2"></i> Anggota
                </a>
                <a href="peminjaman.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'peminjaman.php' ? 'active' : ''; ?>">
                    <i class="bi bi-bookmark-check me-2"></i> Peminjaman
                </a>
                <a href="pengembalian.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'pengembalian.php' ? 'active' : ''; ?>">
                    <i class="bi bi-bookmark-check-fill me-2"></i> Pengembalian
                </a>
                <a href="laporan.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'laporan.php' ? 'active' : ''; ?>">
                    <i class="bi bi-file-earmark-text me-2"></i> Laporan
                </a>
            <?php endif; ?>
            <a href="profile.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'profile.php' ? 'active' : ''; ?>">
                <i class="bi bi-person me-2"></i> Profil
            </a>
            <a href="logout.php" class="nav-link">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </a>
        </nav>
    </div>

    <div class="main-content" id="main-content">
        <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
            <div class="container-fluid">
                <button class="btn btn-link text-dark d-md-none" onclick="toggleSidebar()">
                    <i class="bi bi-list"></i>
                </button>
                <div class="navbar-nav ms-auto">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <img src="<?php echo htmlspecialchars($_SESSION['foto']); ?>" alt="User" class="rounded-circle" style="width: 35px; height: 35px; object-fit: cover;">
                            <span class="ms-2"><?php echo htmlspecialchars($_SESSION['nama']); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i> Profil</a></li>
                            <li><a class="dropdown-item" href="edit_profile.php"><i class="bi bi-pencil me-2"></i> Edit Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <div class="container-fluid mt-4">
            <div class="row">
                <div class="col-12">
                    <h2 class="mb-4"><?php echo isset($title) ? htmlspecialchars($title) : 'Dashboard'; ?></h2>
                </div>
            </div>

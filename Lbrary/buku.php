<?php

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug session
error_log("Session contents: " . print_r($_SESSION, true));

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : null;
$user_name = isset($_SESSION['name']) ? $_SESSION['name'] : null;

// Debug login status
error_log("Login status - is_logged_in: " . ($is_logged_in ? 'true' : 'false'));
error_log("User role: " . $user_role);
error_log("User name: " . $user_name);

include 'config/koneksi.php';
// Remove session check for public access
// include 'config/session_check.php';

// Remove the automatic redirection after login
// This allows users to stay on the book page after logging in
// if ($is_logged_in) {
//     switch ($user_role) {
//         case 'admin':
//             header("Location: dashboard_admin.php");
//             break;
//         case 'petugas':
//             header("Location: dashboard_petugas.php");
//             break;
//         case 'siswa':
//             header("Location: dashboard_user.php");
//             break;
//     }
//     exit;
// }

include 'config/controller.php';

// Add necessary functions
function getCurrentRole() {
    return isset($_SESSION['role']) ? $_SESSION['role'] : null;
}

// Initialize variables
$current_role = null;
$profile = null;
$last_read_book = null;

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    $current_role = getCurrentRole();
    
    // Get student's profile
    $user_id = $_SESSION['user_id'];
    $query = "SELECT * FROM user WHERE id_user = '$user_id'";
    $profile = select($query)[0];

}



// Get all books from database with category
$query = "SELECT b.*, k.nama_kategori
          FROM buku b 
          LEFT JOIN kategori k ON b.id_kategori = k.id_kategori 
          ORDER BY b.judul ASC";
$data_buku = select($query);

// Get all categories for filter
$kategori = select("SELECT * FROM kategori ORDER BY nama_kategori ASC");

?>

<!DOCTYPE html>
<html lang="id" class="scroll-smooth" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Buku | Lbrary</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --bg-color: #252525;
            --text-color: #fef3c7;
            --accent-color: #FFD700;
            --accent-hover: #FFF8DC;
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
            --scrollbar-track: #1A1A1A;
            --scrollbar-thumb: #333333;
            --scrollbar-thumb-hover: #444444;
            --accent-blue: #29C5F6;
        }

        /* Custom Scrollbar Styles */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: var(--scrollbar-track);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--scrollbar-thumb);
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--scrollbar-thumb-hover);
        }

        * {
            scrollbar-width: thin;
            scrollbar-color: var(--scrollbar-thumb) var(--scrollbar-track);
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .btn-detail-buku {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 0.75rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            gap: 0.5rem;
            background: var(--accent-primary);
            color: black;

        }

        .btn-detail-buku:hover {
            background: var(--accent-secondary);
            transform: translateY(-2px);

            color: white;
        }

        .navbar {
            background: var(--nav-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 50;
            padding: 0.75rem 0;
        }

        .navbar-brand {
            color: var(--text-primary);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.25rem;
            padding: 0.5rem 0;
        }

        .navbar-toggler {
            border: none;
            padding: 0.5rem;
            color: var(--text-primary);
            font-size: 1.5rem;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        .nav-link {
            padding: 0.5rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-secondary);
            transition: all 0.3s ease;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--accent-primary);
            background: var(--glass-bg);
        }

        .dropdown-menu {
            min-width: 200px;
            padding: 0.5rem;
            margin-top: 0.5rem;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            box-shadow: var(--glass-shadow);
        }

        .dropdown-item {
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background: var(--glass-bg);
            color: var(--accent-primary);
        }

        .dropdown-divider {
            margin: 0.5rem 0;
            border-color: var(--border-color);
        }

        @media (max-width: 768px) {
            .navbar-collapse {
                background: var(--card-bg);
                padding: 1rem;
                border-radius: 0.5rem;
                margin-top: 0.5rem;
                border: 1px solid var(--border-color);
            }
        }

        /* Add margin-top to main container to account for fixed navbar */
        .main-container {
            margin-top: 76px;
        }

        .page-title {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .page-title h1 {
            font-size: 2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--accent-primary);
        }

        .page-title p {
            color: var(--text-secondary);
            margin: 0;
        }

        .search-section {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--glass-shadow);
        }

        .search-box {
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .search-box:focus {
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.2);
            outline: none;
        }

        .search-box::placeholder {
            color: var(--text-secondary);
        }

        .form-select {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .form-select:focus {
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.2);
        }

        .form-select option {
            background-color: var(--bg-primary);
            color: var(--text-primary);
        }

        .input-group-text {
            background-color: var(--bg-primary);
            border: 1px solid var(--border-color);
            color: var(--accent-primary);
        }

        .book-card {
            position: relative;
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
            cursor: pointer;
        }

        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--glass-shadow);
            border-color: var(--accent-primary);
        }

        .book-cover {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 15px 15px 0 0;
        }

        .book-info {
            padding: 1rem;
        }

        .book-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--accent-primary);
        }

        .book-author {
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .book-details {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            padding: 1.5rem;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .book-card:hover .book-details {
            opacity: 1;
            visibility: visible;
        }

        .details-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--accent-primary);
        }

        .details-meta {
            font-size: 0.9rem;
            color: var(--accent-secondary);
            margin-bottom: 0.5rem;
        }

        .details-meta i {
            width: 20px;
            text-align: center;
            margin-right: 0.5rem;
            color: var(--accent-primary);
        }

        .instruction-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-top: 1px solid var(--glass-border);
            padding: 1rem;
            text-align: center;
            color: var(--accent-primary);
            font-size: 0.9rem;
            z-index: 1000;
            animation: fadeInUp 0.5s ease;
        }

        @keyframes fadeInUp {
            from {
                transform: translateY(100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .instruction-bar i {
            margin-right: 0.5rem;
            color: var(--accent-primary);
        }

        .continue-reading-btn {
            position: fixed;
            bottom: 40px;
            right: 20px;
            z-index: 1;
        }

        .continue-reading-btn .btn {
            background: var(--accent-primary);
            border: none;
            border-radius: 50px;
            padding: 12px 24px;
            color: #000;
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .continue-reading-btn .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 215, 0, 0.4);
            background: var(--accent-secondary);
            color: #000;
        }

        .continue-reading-btn .btn i {
            font-size: 1.2rem;
        }

        .continue-reading-btn .btn span {
            font-weight: 500;
        }

        .continue-reading-btn .btn small {
            display: block;
            font-size: 0.8rem;
            opacity: 0.8;
            margin-top: 2px;
        }

        /* Update SweetAlert2 theme colors */
        .swal2-popup {
            background: var(--bg-secondary) !important;
            color: var(--text-primary) !important;
        }

        .swal2-title {
            color: var(--accent-primary) !important;
        }

        .swal2-confirm {
            background: var(--accent-primary) !important;
            color: #000 !important;
        }

        .swal2-cancel {
            background: var(--accent-secondary) !important;
            color: #000 !important;
        }

        @media (max-width: 768px) {
            .continue-reading-btn {
                bottom: 70px;
                right: 10px;
            }

            .continue-reading-btn .btn {
                padding: 8px 16px;
            }

            .continue-reading-btn .btn small {
                display: none;
            }
        }

        /* Modern Mobile Menu */
        .mobile-menu {
            background: var(--bg-primary);
            border-left: 1px solid var(--border-color);
            position: fixed;
            top: 0;
            right: -100%;
            width: 80%;
            height: 100vh;
            padding: 2rem;
            transition: right 0.3s ease;
            z-index: 100;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        .mobile-menu.active {
            right: 0;
        }

        /* Modern Theme Toggle */
        #themeToggle {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: var(--accent-primary);
            color: black;
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            transition: all 0.3s ease;
            z-index: 50;
            cursor: pointer;
            border: none;
            outline: none;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        #themeToggle:hover {
            background: var(--accent-secondary);
            transform: translateY(-3px) rotate(180deg);
        }

        /* Add margin-top to main container to account for fixed navbar */
        .main-container {
            margin-top: 100px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .mobile-menu {
                display: block;
            }
        }

        /* Navbar Styles */
        .glass {
            background: rgba(10, 10, 10, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 50;
            padding: 0.5rem 0;
        }

        /* Dropdown Styles */
        .group:hover .group-hover\:opacity-100 {
            opacity: 1;
        }

        .group:hover .group-hover\:visible {
            visibility: visible;
        }

        .group:hover .group-hover\:translate-y-0 {
            transform: translateY(0);
        }

        /* Ensure dropdowns are above other content */
        .relative {
            position: relative;
        }

        .absolute {
            position: absolute;
        }

        .z-50 {
            z-index: 50;
        }

        /* Transition effects */
        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 300ms;
        }

        /* Hover effects */
        .hover\:bg-primary-500:hover {
            background-color: var(--accent-primary);
        }

        .hover\:text-white:hover {
            color: white;
        }

        /* Shadow effects */
        .shadow-lg {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        /* Modal Styles */
        .modal-content {
            background: rgba(31, 41, 55, 0.95) !important;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .form-control {
            background-color: rgba(55, 65, 81, 0.5) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: white !important;
        }

        .form-control:focus {
            background-color: rgba(55, 65, 81, 0.7) !important;
            border-color: var(--accent-primary) !important;
            color: white !important;
            box-shadow: 0 0 0 0.2rem rgba(255, 215, 0, 0.25) !important;
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1) !important;
            border: 1px solid rgba(220, 53, 69, 0.2) !important;
            color: #ff6b6b !important;
        }

        .btn-primary {
            background-color: var(--accent-primary) !important;
            border: none !important;
            color: black !important;
        }

        .btn-primary:hover {
            background-color: var(--accent-secondary) !important;
            transform: translateY(-1px);
        }

        .btn-primary:disabled {
            background-color: var(--accent-primary) !important;
            opacity: 0.7;
        }

        .btn-primary {
            background-color: var(--accent-primary) !important;
            border: none !important;
            color: black !important;
        }

        .btn-primary:hover {
            background-color: var(--accent-secondary) !important;
            transform: translateY(-1px);
        }

        .btn-primary:disabled {
            background-color: var(--accent-primary) !important;
            opacity: 0.7;
        }

        .btn-primary {
            background-color: var(--accent-primary) !important;
            border: none !important;
            color: black !important;
        }

        .btn-primary:hover {
            background-color: var(--accent-secondary) !important;
            transform: translateY(-1px);
        }

        .btn-primary:disabled {
            background-color: var(--accent-primary) !important;
            opacity: 0.7;
        }

        .btn-blue {
            background-color: var(--accent-blue) !important;
            border: none !important;
            color: white !important;
        }

        .btn-blue:hover {
            background-color: var(--accent-blue) !important;
            transform: translateY(-1px);
        }

        .btn-blue:disabled {
            background-color: var(--accent-blue) !important;
            opacity: 0.7;
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
            <!-- Desktop Navigation -->
            <div class="d-none d-md-flex align-items-center">
                <a href="index.php" class="nav-link">Beranda</a>
                <a href="buku.php" class="nav-link active">Katalog Buku</a>
                
                <!-- Dropdown Menu -->
                <div class="dropdown">
                    <button class="nav-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Fitur
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="index.php#fitur">Fitur Unggulan</a></li>
                        <li><a class="dropdown-item" href="index.php#statistik">Statistik</a></li>
                        <li><a class="dropdown-item" href="index.php#event">Event</a></li>
                    </ul>
                </div>

                <!-- Dropdown Menu -->
                <div class="dropdown">
                    <button class="nav-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Informasi
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="index.php#tentang">Tentang</a></li>
                        <li><a class="dropdown-item" href="index.php#komentar">Komentar</a></li>
                        <li><a class="dropdown-item" href="index.php#kontak">Kontak</a></li>
                    </ul>
                </div>
            </div>
            <!-- Desktop Profile -->
            <div class="d-none d-md-flex align-items-center ms-auto">
                <?php if ($is_logged_in): ?>
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <img src="<?= !empty($profile['foto']) ? 'uploads/profile/' . $profile['foto'] : 'assets/img/default-avatar.png' ?>" 
                                 alt="Profile" class="rounded-circle" width="35" height="35" style="object-fit: cover;">
                            <span class="d-none d-lg-inline"><?= htmlspecialchars($user_name ?? '') ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if ($user_role === 'user'): ?>
                                <!-- <li><a class="dropdown-item" href="dashboard_user.php"><i class="ri-dashboard-line me-2"></i>Dashboard</a></li> -->
                            <?php elseif ($user_role === 'admin'): ?>
                                <li><a class="dropdown-item" href="dashboard_admin.php"><i class="ri-dashboard-line me-2"></i>Dashboard</a></li>
                            <?php elseif ($user_role === 'petugas'): ?>
                                <li><a class="dropdown-item" href="dashboard_petugas.php"><i class="ri-dashboard-line me-2"></i>Dashboard</a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="edit_profile.php"><i class="ri-user-settings-line me-2"></i>Edit Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="ri-logout-box-line me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">
                        Login
                    </button>
                <?php endif; ?>
            </div>
            <!-- Mobile Profile (Collapsible) -->
            <div class="collapse navbar-collapse d-md-none" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="buku.php">Katalog Buku</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Fitur
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="index.php#fitur">Fitur Unggulan</a></li>
                            <li><a class="dropdown-item" href="index.php#statistik">Statistik</a></li>
                            <li><a class="dropdown-item" href="index.php#event">Event</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            Informasi
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="index.php#tentang">Tentang</a></li>
                            <li><a class="dropdown-item" href="index.php#komentar">Komentar</a></li>
                            <li><a class="dropdown-item" href="index.php#kontak">Kontak</a></li>
                        </ul>
                    </li>
                    <?php if ($is_logged_in): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="userDropdownMobile" role="button" data-bs-toggle="dropdown">
                                <img src="<?= !empty($profile['foto']) ? 'uploads/profile/' . $profile['foto'] : 'assets/img/default-avatar.png' ?>" 
                                     alt="Profile" class="rounded-circle" width="35" height="35" style="object-fit: cover;">
                                <?= htmlspecialchars($user_name ?? '') ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php if ($user_role === 'user'): ?>
                                    <li><a class="dropdown-item" href="dashboard_user.php"><i class="ri-dashboard-line me-2"></i>Dashboard</a></li>
                                <?php elseif ($user_role === 'admin'): ?>
                                    <li><a class="dropdown-item" href="dashboard_admin.php"><i class="ri-dashboard-line me-2"></i>Dashboard</a></li>
                                <?php elseif ($user_role === 'petugas'): ?>
                                    <li><a class="dropdown-item" href="dashboard_petugas.php"><i class="ri-dashboard-line me-2"></i>Dashboard</a></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="edit_profile.php"><i class="ri-user-settings-line me-2"></i>Edit Profil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="ri-logout-box-line me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#loginModal">
                                Login
                            </button>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Modern Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-xl font-bold">Menu</h2>
            <button class="text-white" id="closeMenuBtn">
                <i class="ri-close-line text-2xl"></i>
            </button>
        </div>
        <div class="flex flex-col space-y-4">
            <?php if ($is_logged_in): ?>
                <div class="flex items-center space-x-2 mb-4">
                    <i class="ri-user-line text-xl"></i>
                    <span class="font-semibold"><?php echo htmlspecialchars($user_name ?? ''); ?></span>
                </div>
                <?php if ($user_role === 'user'): ?>
                    <a href="dashboard_user.php" class="text-white hover:text-primary-400 transition">
                        <i class="ri-dashboard-line mr-2"></i>Dashboard
                    </a>
                    <a href="edit_profile.php" class="text-white hover:text-primary-400 transition">
                        <i class="ri-user-settings-line mr-2"></i>Edit Profile
                    </a>
                <?php elseif ($user_role === 'admin'): ?>
                    <a href="dashboard_admin.php" class="text-white hover:text-primary-400 transition">
                        <i class="ri-dashboard-line mr-2"></i>Dashboard
                    </a>
                    <a href="edit_profile_admin.php" class="text-white hover:text-primary-400 transition">
                        <i class="ri-user-settings-line mr-2"></i>Edit Profile
                    </a>
                <?php elseif ($user_role === 'petugas'): ?>
                    <a href="dashboard_petugas.php" class="text-white hover:text-primary-400 transition">
                        <i class="ri-dashboard-line mr-2"></i>Dashboard
                    </a>
                    <a href="edit_profile_petugas.php" class="text-white hover:text-primary-400 transition">
                        <i class="ri-user-settings-line mr-2"></i>Edit Profile
                    </a>
                <?php endif; ?>
                <a href="index.php" class="text-white hover:text-primary-400 transition">
                    <i class="ri-home-line mr-2"></i>Home
                </a>
                <a href="logout.php" class="text-white hover:text-primary-400 transition">
                    <i class="ri-logout-box-line mr-2"></i>Logout
                </a>
            <?php else: ?>
                <a href="index.php" class="text-white hover:text-primary-400 transition">Beranda</a>
                <a href="buku.php" class="text-white hover:text-primary-400 transition">Katalog Buku</a>
                <a href="login.php" class="text-white hover:text-primary-400 transition">Login</a>
            <?php endif; ?>
        </div>
    </div>


    <div class="main-container">
        <div class="container">
            <!-- Page Title -->
            <div class="page-title">
                <h1>Daftar Buku</h1>
                <p>Jelajahi koleksi buku perpustakaan kami</p>
            </div>

            <!-- Search Section -->
            <div class="search-section">
            <div class="row g-3">
                    <div class="col-md-9">
                    <div class="input-group">
                            <span class="input-group-text border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control search-box border-start-0" id="searchInput" placeholder="Cari buku...">
                    </div>
                </div>
                    <div class="col-md-3">
                    <select class="form-select search-box" id="categoryFilter">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($kategori as $kat): ?>
                                <option value="<?= $kat['nama_kategori'] ?>"><?= $kat['nama_kategori'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                
            </div>
        </div>

        <!-- Books Grid -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4" id="booksGrid">
            <?php if (empty($data_buku)): ?>
                <div class="col-12">
                        <div class="text-center py-5">
                            <i class="bi bi-book display-1 text-muted"></i>
                            <h3 class="mt-3">Tidak ada buku yang tersedia</h3>
                        <p class="text-muted">Silakan tambahkan buku baru untuk memulai</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($data_buku as $buku): ?>
                        <div class="col">
                            <div class="book-card" data-book='<?= json_encode([
                                "id" => $buku['id_buku'],
                                "judul" => $buku['judul'],
                                "penulis" => $buku['penulis'],
                                "penerbit" => $buku['penerbit'],
                                "tahun" => $buku['tahun'],
                                "kategori" => $buku['nama_kategori'],
                                "gambar" => $buku['gambar'],
                                "pdf_file" => $buku['pdf_file'],
                                "deskripsi" => $buku['deskripsi']
                                
                            ]) ?>'>
                                <img src="<?= !empty($buku['gambar']) ? 'uploads/books/' . $buku['gambar'] : 'https://via.placeholder.com/300x200?text=' . urlencode($buku['judul']) ?>" 
                                     class="book-cover" 
                                 alt="<?= htmlspecialchars($buku['judul']) ?>">
                                <div class="book-info">
                                <h5 class="book-title"><?= htmlspecialchars($buku['judul']) ?></h5>
                                <p class="book-author">
                                        <i class="bi bi-person"></i><?= htmlspecialchars($buku['penulis']) ?>
                                    </p>
                                    
                                </div>
                                <div class="book-details">
                                    <h3 class="details-title"><?= htmlspecialchars($buku['judul']) ?></h3>
                                    <div class="details-meta">
                                        <p><i class="bi bi-person"></i><?= htmlspecialchars($buku['penulis']) ?></p>
                                        <p><i class="bi bi-building"></i><?= htmlspecialchars($buku['penerbit']) ?></p>
                                        <p><i class="bi bi-calendar"></i><?= htmlspecialchars($buku['tahun']) ?></p>
                                        <p><i class="bi bi-folder"></i><?= htmlspecialchars($buku['nama_kategori']) ?></p>
                                        <p style="opacity: 0.7;"><?= htmlspecialchars($buku['deskripsi']) ?></p>
                                        
                                        <div class="text-center mt-3">
    <?php if (!$is_logged_in): ?>
        <!-- Jika belum login -->
        <button class="btn btn-danger w-100 mt-2 btn-show-login-modal">
            <i class="bi bi-lock"></i> Login untuk Membaca
        </button>
    <?php elseif (!empty($buku['pdf_file'])): ?>
        <!-- Jika login dan file ada -->
        <a href="uploads/books/<?= urlencode($buku['pdf_file']) ?>" 
           class="btn btn-success w-100 mt-2" 
           target="_blank" 
           download>
            <i class="bi bi-download"></i> Download PDF
        </a>
        <a href="pdf_reader.php?id=<?= $buku['id_buku'] ?>" 
           target="_blank" 
           class="btn btn-blue w-100 mt-2">
           <i class="bi bi-book"></i> Baca Buku
        </a>
    <?php else: ?>
        <!-- Jika login tapi file tidak ada -->
        <button class="btn btn-secondary w-100 mt-2" disabled>
            <i class="bi bi-ban"></i> File Belum Tersedia
        </button>
    <?php endif; ?>
</div>


                                    </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Instruction Bar -->
    <div class="instruction-bar">
        <i class="bi bi-mouse"></i>Arahkan ke buku untuk membaca
    </div>

    
    <!-- Continue Reading Button -->
    <?php if ($is_logged_in && $last_read_book): ?>
    <!-- <div class="continue-reading-btn" data-aos="fade-up" data-aos-delay="400">
        <button class="btn btn-primary" onclick="continueReading(<?= htmlspecialchars(json_encode([
            'id' => $last_read_book['id_buku'],
            'judul' => $last_read_book['judul'],
            'pdf_file' => $last_read_book['pdf_file']
        ])) ?>)">
            <i class="bi bi-book"></i>
            <span>Lanjutkan Membaca</span>
            <small><?= htmlspecialchars($last_read_book['judul']) ?></small>
        </button>
    </div> -->
    <?php endif; ?> 


    <!-- Include Modals -->
    <?php include 'includes/modals.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize GSAP
        gsap.utils.toArray('.book-card').forEach(card => {
    gsap.from(card, {
        scrollTrigger: {
            trigger: card,
            start: 'top 90%',
        },
        y: 50,
        opacity: 0,
        duration: 0.5,
        ease: 'power2.out'
    });
});
document.addEventListener("DOMContentLoaded", () => {


    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const booksGrid = document.getElementById('booksGrid');

    console.log("searchInput:", searchInput);
    console.log("categoryFilter:", categoryFilter);
    console.log("booksGrid:", booksGrid);

    if (!searchInput || !categoryFilter || !booksGrid) {
        console.error("Salah satu elemen tidak ditemukan di DOM");
        return;
    }

    function filterBooks() {
        const searchTerm = searchInput.value.toLowerCase();
        const category = categoryFilter.value;

        const bookCards = booksGrid.getElementsByClassName('book-card');

        Array.from(bookCards).forEach(card => {
            const bookData = JSON.parse(card.dataset.book);
            const title = bookData.judul.toLowerCase();
            const bookCategory = bookData.kategori;

            const matchesSearch = title.includes(searchTerm);
            const matchesCategory = !category || bookCategory.includes(category);
            const matchesStock = true;

            card.parentElement.style.display = matchesSearch && matchesCategory && matchesStock ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterBooks);
    categoryFilter.addEventListener('change', filterBooks);

    filterBooks(); // Inisialisasi filter awal
});

                // Show book details first
                Swal.fire({
                    title: bookData.judul,
                    html: `
                        <div class="text-start">
                            <p><strong>Penulis:</strong> ${bookData.penulis}</p>
                            <p><strong>Penerbit:</strong> ${bookData.penerbit}</p>
                            <p><strong>Tahun:</strong> ${bookData.tahun}</p>
                            <p><strong>Kategori:</strong> ${bookData.kategori}</p>
                        </div>
                    `,
                    showCancelButton: true,
                    showDenyButton: true,
                    confirmButtonText: 'Baca Buku',
                    denyButtonText: 'Download PDF',
                    cancelButtonText: 'Tutup',
                    background: '#1A1A1A',
                    color: '#FFFFFF',
                    confirmButtonColor: '#FFD700',
                    denyButtonColor: '#4CAF50',
                    cancelButtonColor: '#FFF8DC'
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (bookData.pdf_file) {
                            <?php if ($is_logged_in): ?>
                                // Track reading start
                                fetch('buku.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                    },
                                    body: `start_reading=1&id_buku=${bookData.id}`
                                }).then(() => {
                                    // Open custom PDF reader
                                    window.open(`pdf_reader.php?id=${bookData.id}`, '_blank', 'width=1200,height=800');
                                });
                            <?php else: ?>
                                Swal.fire({
                                    title: 'Login Diperlukan',
                                    text: 'Silakan login terlebih dahulu untuk membaca buku ini.',
                                    icon: 'info',
                                    showCancelButton: true,
                                    confirmButtonText: 'Login',
                                    cancelButtonText: 'Batal',
                                    background: '#1A1A1A',
                                    color: '#FFFFFF',
                                    confirmButtonColor: '#FFD700',
                                    cancelButtonColor: '#FFF8DC'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // Show login modal
                                        const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                                        loginModal.show();
                                    }
                                });
                            <?php endif; ?>
                        } else {
                            Swal.fire({
                                title: 'PDF Tidak Tersedia',
                                text: 'File PDF untuk buku ini belum tersedia.',
                                icon: 'info',
                                confirmButtonText: 'OK',
                                background: '#1A1A1A',
                                color: '#FFFFFF'
                            });
                        }
                    } else if (result.isDenied) {
                        if (bookData.pdf_file) {
                            <?php if ($is_logged_in): ?>
                                // Create a temporary link and trigger download
                                const link = document.createElement('a');
                                link.href = `uploads/books/${bookData.pdf_file}`;
                                link.download = `${bookData.judul}.pdf`;
                                document.body.appendChild(link);
                                link.click();
                                document.body.removeChild(link);
                            <?php else: ?>
                                Swal.fire({
                                    title: 'Login Diperlukan',
                                    text: 'Silakan login terlebih dahulu untuk mengunduh buku ini.',
                                    icon: 'info',
                                    showCancelButton: true,
                                    confirmButtonText: 'Login',
                                    cancelButtonText: 'Batal',
                                    background: '#1A1A1A',
                                    color: '#FFFFFF',
                                    confirmButtonColor: '#FFD700',
                                    cancelButtonColor: '#FFF8DC'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // Show login modal
                                        const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                                        loginModal.show();
                                    }
                                });
                            <?php endif; ?>
                        } else {
                            Swal.fire({
                                title: 'PDF Tidak Tersedia',
                                text: 'File PDF untuk buku ini belum tersedia.',
                                icon: 'info',
                                confirmButtonText: 'OK',
                                background: '#1A1A1A',
                                color: '#FFFFFF'
                            });
                        }
                    }
                });
            });
        });

        // Modified continue reading function
        function continueReading(bookData) {
            <?php if ($is_logged_in): ?>
                if (bookData.pdf_file) {
                    // Track reading start
                    fetch('buku.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `start_reading=1&id_buku=${bookData.id}`
                    }).then(() => {
                        // Open custom PDF reader
                        window.open(`pdf_reader.php?id=${bookData.id}`, '_blank', 'width=1200,height=1200');
                    });
                }
            <?php else: ?>
                Swal.fire({
                    title: 'Login Diperlukan',
                    text: 'Silakan login terlebih dahulu untuk membaca buku ini.',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Login',
                    cancelButtonText: 'Batal',
                    background: '#1A1A1A',
                    color: '#FFFFFF',
                    confirmButtonColor: '#FFD700',
                    cancelButtonColor: '#FFF8DC'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show login modal
                        const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                        loginModal.show();
                    }
                });
            <?php endif; ?>
        }

        // Mobile Menu
        const mobileMenu = document.getElementById('mobileMenu');
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const closeMenuBtn = document.getElementById('closeMenuBtn');

        function toggleMenu() {
            mobileMenu.classList.toggle('active');
            document.body.style.overflow = mobileMenu.classList.contains('active') ? 'hidden' : '';
        }

        mobileMenuBtn.addEventListener('click', toggleMenu);
        closeMenuBtn.addEventListener('click', toggleMenu);

        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!mobileMenu.contains(e.target) && !mobileMenuBtn.contains(e.target)) {
                mobileMenu.classList.remove('active');
                document.body.style.overflow = '';
            }
        });

        // Theme Toggle
        const themeToggle = document.getElementById('themeToggle');
        const html = document.documentElement;
        const icon = themeToggle.querySelector('i');

        function setTheme(theme) {
            html.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            icon.className = theme === 'light' ? 'ri-sun-line' : 'ri-moon-line';
            
            // Add transition class
            html.classList.add('theme-transition');
            setTimeout(() => html.classList.remove('theme-transition'), 1000);
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

        // Modal Initialization
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all modals
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                new bootstrap.Modal(modal);
            });

            // Login form handling
            const loginForm = document.getElementById('loginForm');
            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const loginError = document.getElementById('loginError');
                    const errorMessage = document.getElementById('errorMessage');
                    const submitButton = this.querySelector('button[type="submit"]');
                    const spinner = submitButton.querySelector('.spinner-border');
                    const buttonText = submitButton.querySelector('.button-text');
                    
                    // Show loading state
                    spinner.classList.remove('d-none');
                    buttonText.textContent = 'Logging in...';
                    submitButton.disabled = true;
                    
                    fetch('includes/login_process.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            window.location.reload();
                        } else {
                            errorMessage.textContent = data.message;
                            loginError.classList.remove('d-none');
                            
                            // Reset button state
                            spinner.classList.add('d-none');
                            buttonText.textContent = 'Login';
                            submitButton.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        errorMessage.textContent = 'Terjadi kesalahan. Silakan coba lagi.';
                        loginError.classList.remove('d-none');
                        
                        // Reset button state
                        spinner.classList.add('d-none');
                        buttonText.textContent = 'Login';
                        submitButton.disabled = false;
                    });
                });
            }

            // Show/Hide Password
            const showPasswordCheckbox = document.getElementById('showPassword');
            const passwordInput = document.getElementById('password');
            
            if (showPasswordCheckbox && passwordInput) {
                showPasswordCheckbox.addEventListener('change', function() {
                    passwordInput.type = this.checked ? 'text' : 'password';
                });
            }

            // Reset form when modal is closed
            const loginModal = document.getElementById('loginModal');
            if (loginModal) {
                loginModal.addEventListener('hidden.bs.modal', function () {
                    if (loginForm) {
                        loginForm.reset();
                        const loginError = document.getElementById('loginError');
                        const errorMessage = document.getElementById('errorMessage');
                        const submitButton = loginForm.querySelector('button[type="submit"]');
                        const spinner = submitButton.querySelector('.spinner-border');
                        const buttonText = submitButton.querySelector('.button-text');
                        
                        loginError.classList.add('d-none');
                        errorMessage.textContent = '';
                        spinner.classList.add('d-none');
                        buttonText.textContent = 'Login';
                        submitButton.disabled = false;
                    }
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            
            // Handle dropdown menus
            const dropdowns = document.querySelectorAll('.group');
            
            dropdowns.forEach(dropdown => {
                const button = dropdown.querySelector('button');
                const menu = dropdown.querySelector('.group-hover\\:opacity-100');
                
                if (button && menu) {
                    // Show menu on hover
                    dropdown.addEventListener('mouseenter', () => {
                        menu.classList.remove('invisible', 'opacity-0');
                        menu.classList.add('visible', 'opacity-100');
                    });
                    
                    // Hide menu when mouse leaves
                    dropdown.addEventListener('mouseleave', () => {
                        menu.classList.add('invisible', 'opacity-0');
                        menu.classList.remove('visible', 'opacity-100');
                    });
                }
            });
        });
    </script>
  
  <script>
    document.querySelectorAll('.btn-detail-buku').forEach(button => {
        button.addEventListener('click', () => {
            const bookData = JSON.parse(button.dataset.book);

            Swal.fire({
                title: bookData.judul,
                html: `
                    <div class="text-start">
                        <p><strong>Penulis:</strong> ${bookData.penulis}</p>
                        <p><strong>Penerbit:</strong> ${bookData.penerbit}</p>
                        <p><strong>Tahun:</strong> ${bookData.tahun}</p>
                        <p><strong>Kategori:</strong> ${bookData.kategori}</p>
                    </div>
                `,
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: 'Baca Buku',
                denyButtonText: 'Download PDF',
                cancelButtonText: 'Tutup',
                background: '#1A1A1A',
                color: '#FFFFFF',
                confirmButtonColor: '#FFD700',
                denyButtonColor: '#4CAF50',
                cancelButtonColor: '#FFF8DC'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (bookData.pdf_file) {
                        <?php if ($is_logged_in): ?>
                            fetch('buku.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: `start_reading=1&id_buku=${bookData.id}`
                            }).then(() => {
                                window.open(`pdf_reader.php?id=${bookData.id}`, '_blank', 'width=1200,height=800');
                            });
                        <?php else: ?>
                            Swal.fire({
                                title: 'Login Diperlukan',
                                text: 'Silakan login terlebih dahulu untuk membaca buku ini.',
                                icon: 'info',
                                showCancelButton: true,
                                confirmButtonText: 'Login',
                                cancelButtonText: 'Batal',
                                background: '#1A1A1A',
                                color: '#FFFFFF',
                                confirmButtonColor: '#FFD700',
                                cancelButtonColor: '#FFF8DC'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                                    loginModal.show();
                                }
                            });
                        <?php endif; ?>
                    } else {
                        Swal.fire({
                            title: 'PDF Tidak Tersedia',
                            text: 'File PDF untuk buku ini belum tersedia.',
                            icon: 'info',
                            confirmButtonText: 'OK',
                            background: '#1A1A1A',
                            color: '#FFFFFF'
                        });
                    }
                } else if (result.isDenied) {
                    if (bookData.pdf_file) {
                        <?php if ($is_logged_in): ?>
                            const link = document.createElement('a');
                            link.href = `uploads/books/${bookData.pdf_file}`;
                            link.download = `${bookData.judul}.pdf`;
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        <?php else: ?>
                            Swal.fire({
                                title: 'Login Diperlukan',
                                text: 'Silakan login terlebih dahulu untuk mengunduh buku ini.',
                                icon: 'info',
                                showCancelButton: true,
                                confirmButtonText: 'Login',
                                cancelButtonText: 'Batal',
                                background: '#1A1A1A',
                                color: '#FFFFFF',
                                confirmButtonColor: '#FFD700',
                                cancelButtonColor: '#FFF8DC'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                                    loginModal.show();
                                }
                            });
                        <?php endif; ?>
                    } else {
                        Swal.fire({
                            title: 'PDF Tidak Tersedia',
                            text: 'File PDF untuk buku ini belum tersedia.',
                            icon: 'info',
                            confirmButtonText: 'OK',
                            background: '#1A1A1A',
                            color: '#FFFFFF'
                        });
                    }
                }
            });
        });
    });
</script>

<script>
    document.querySelectorAll('.btn-show-login-modal').forEach(button => {
        button.addEventListener('click', () => {
            const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
        });
    });
</script>
</body>
</html> 
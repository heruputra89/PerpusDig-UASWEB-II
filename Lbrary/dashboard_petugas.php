<?php
require_once 'config/session_check.php';
require_once 'config/controller.php';
require_once 'config/common_functions.php';
checkRole('petugas');

// Get admin's profile
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM user WHERE id_user = '$user_id'";
$profile = select($query)[0];

// Get all data for management
$data_akun = select("SELECT * FROM user");
$data_kategori = select("SELECT * FROM kategori");
$data_buku = select("SELECT b.*, k.nama_kategori
                    FROM buku b 
                    LEFT JOIN kategori k ON b.id_kategori = k.id_kategori 
                    ORDER BY b.judul ASC");

// Get statistics
$total_users = count($data_akun);
$total_books = count($data_buku);
$total_categories = count($data_kategori);
$active_users = count(array_filter($data_akun, function($user) {
    return strtolower($user['status']) === 'aktif';
}));

// Get book statistics
$total_stock = array_sum(array_column($data_buku, 'status'));
$available_books = count(array_filter($data_buku, function($buku) {
    return isset($buku['status']) && $buku['status'] === "Tersedia";
}));

?>

<!DOCTYPE html>
<html lang="id" class="scroll-smooth" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petugas - Lbrary</title>
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
                            50: '#FFFBEB',
                            100: '#FEF3C7',
                            200: '#FDE68A',
                            300: '#FCD34D',
                            400: '#FBBF24',
                            500: '#F59E0B',
                            600: '#D97706',
                            700: '#B45309',
                            800: '#92400E',
                            900: '#78350F',
                        },
                        secondary: {
                            50: '#FFF8DC',
                            100: '#FFF8DC',
                            200: '#FFF8DC',
                            300: '#FFF8DC',
                            400: '#FFF8DC',
                            500: '#FFF8DC',
                            600: '#FFF8DC',
                            700: '#FFF8DC',
                            800: '#FFF8DC',
                            900: '#FFF8DC',
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
    <!-- AOS Modern Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
            overflow-x: hidden;
            padding-top: 4rem;
            scroll-padding-top: 4rem;
            line-height: 1.6;
            transition: all 0.3s ease;
        }

        /* Modern Glassmorphism */
        .glass {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            box-shadow: var(--glass-shadow);
        }

        /* Modern Navbar */
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

        @media (max-width: 768px) {
            .navbar-collapse {
                background: var(--card-bg);
                padding: 1rem;
                border-radius: 0.5rem;
                margin-top: 0.5rem;
                border: 1px solid var(--border-color);
            }
        }

        /* Modern Cards */
        .stats-card, .table-card {
            background: var(--card-bg);
            border-radius: 1rem;
            padding: 2rem;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stats-card::before, .table-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--accent-primary);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .stats-card:hover::before, .table-card:hover::before {
            transform: scaleX(1);
        }

        .stats-card:hover, .table-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        /* Modern Buttons */
        .btn-custom {
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

        .btn-custom:hover {
            background: var(--accent-blue);
            transform: translateY(-2px);
            color: black;
        }

        /* Welcome Section */
        .welcome-section {
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            color: black;
            padding: 2rem;
            border-radius: 1rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .welcome-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1), transparent);
            z-index: 1;
        }

        /* Table Styles */
        .table {
            color: var(--text-primary);
            border-color: var(--border-color);
        }

        .table thead {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .table tbody tr:hover {
            background: var(--glass-bg);
        }

        /* Scrollable Area */
        .scrollable {
            max-height: 400px;
            overflow-y: auto;
            
            scrollbar-width: thin;
            scrollbar-color: var(--accent-primary) var(--bg-secondary);
        }

        .scrollable::-webkit-scrollbar {
            width: 6px;
        }

        .scrollable::-webkit-scrollbar-track {
            background: var(--bg-secondary);
        }

        .scrollable::-webkit-scrollbar-thumb {
            background: var(--accent-primary);
            border-radius: 3px;
        }

        /* Badge Styles */
        .badge {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
        }

        .stock-badge {
            padding: 0.5rem 1rem;
            border-radius: 1rem;
            font-weight: 500;
        }

        .stock-low {
            background: #f59e0b;
            color: white;
        }

        .stock-empty {
            background: #ef4444;
            color: white;
        }

        .stock-available {
            background: #22c55e;
            color: white;
        }

        /* Theme Toggle */
        .theme-toggle {
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

        .theme-toggle:hover {
            background: var(--accent-secondary);
            transform: translateY(-3px) rotate(180deg);
        }

        /* Sidebar Navigation */
        .sidebar {
            position: fixed;
            left: 0;
            top: 4rem;
            bottom: 0;
            width: 250px;
            background: var(--card-bg);
            border-right: 1px solid var(--border-color);
            padding: 1rem;
            transition: all 0.3s ease;
            z-index: 40;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
            margin-bottom: 0.5rem;
        }

        .sidebar-link:hover, .sidebar-link.active {
            background: var(--glass-bg);
            color: var(--text-primary);
        }

        .sidebar-link i {
            font-size: 1.25rem;
        }

        /* Main Content Area */
        .main-content {
            margin-left: 250px;
            padding: 2rem;
            transition: all 0.3s ease;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
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
                        <li><a class="dropdown-item" href="edit_profile_petugas.php"><i class="ri-user-settings-line me-2"></i>Edit Profil</a></li>
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
                            <li><a class="dropdown-item" href="edit_profile_admin.php"><i class="ri-user-settings-line me-2"></i>Edit Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="ri-logout-box-line me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <a href="index.php" class="sidebar-link">
            <i class="ri-home-line"></i>
            Home
        </a>
        <a href="buku.php" class="sidebar-link">
            <i class="ri-book-2-line"></i>
            Katalog Buku
        </a>
        <a href="#dashboard" class="sidebar-link active">
            <i class="ri-dashboard-line"></i>
            Dashboard
        </a>
  
        <a href="#buku-section" class="sidebar-link">
            <i class="ri-book-line"></i>
            Manajemen Buku
        </a>
        <a href="#kategori-section" class="sidebar-link">
            <i class="ri-folder-line"></i>
            Manajemen Kategori
        </a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Welcome Section -->
        <div class="welcome-section" data-aos="fade-up">
            <h2><?= getGreeting() ?>, <?= htmlspecialchars($profile['name']) ?>!</h2>
            <p class="mb-0">Anda login sebagai Petugas</p>
            <div class="py-2"></div>
            <p class="datetime-display mb-0">
                <i class="ri-calendar-line me-1"></i><?= getCurrentDateTime() ?>
            </p>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-2 mb-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="stats-card">
                    <i class="ri-book-fill stats-icon"></i>
                    <h3 class="stats-number"><?= $total_books ?></h3>
                    <p class="stats-label">Total Buku</p>
                    <div class="mt-2">
                        <span class="badge bg-success"><?= $available_books ?> Tersedia</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="stats-card">
                    <i class="ri-folder-fill stats-icon"></i>
                    <h3 class="stats-number"><?= $total_categories ?></h3>
                    <p class="stats-label">Kategori Buku</p>
                </div>
            </div>
            
        </div>

        <!-- Book Management Section -->
        <div class="row g-4 mb-4" id="buku-section">
            <div class="col-12" data-aos="fade-up">
                <div class="table-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title mb-0">
                                <i class="bi bi-book me-2"></i>Daftar Buku
                            </h4>
                            <a href="tambahbuku.php" class="btn btn-primary btn-custom">
                            <i class="ri-add-circle-line me-1"></i></i>Tambah Buku
                            </a>
                        </div>
                        <div class="scrollable">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Gambar</th>
                                        <th>Judul</th>
                                        <th>Deskripsi</th>
                                        <th>Penulis</th>
                                        <th>Penerbit</th>
                                        <th>Tahun</th>
                                        <th>Kategori</th>
                                        
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($data_buku) === 0): ?>
                                        <tr>
                                            <td colspan="9" class="text-center text-muted">
                                                <i class="bi bi-book me-2"></i>Tidak ada buku yang tersedia
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach($data_buku as $buku): ?>
                                            <tr>
                                                <td>
                                                    <img src="<?= !empty($buku['gambar']) ? 'uploads/books/' . $buku['gambar'] : 'https://via.placeholder.com/50x50?text=' . urlencode($buku['judul']) ?>" 
                                                         alt="<?= htmlspecialchars($buku['judul']) ?>"
                                                         class="rounded"
                                                         width="50"
                                                         height="50"
                                                         style="object-fit: cover;">
                                                </td>
                                                <td><?= htmlspecialchars($buku['judul']) ?></td>
                                                <td><?= htmlspecialchars($buku['deskripsi']) ?></td>
                                                <td><?= htmlspecialchars($buku['penulis']) ?></td>
                                                <td><?= htmlspecialchars($buku['penerbit']) ?></td>
                                                <td><?= htmlspecialchars($buku['tahun']) ?></td>
                                                <td><?= htmlspecialchars($buku['nama_kategori']) ?></td>
                                                
                                                <td>
                                                    <?php
                                                    $stock_class = 'stock-available';
                                                    if ($buku['status'] == 0) {
                                                        $stock_class = 'stock-empty';
                                                    } elseif ($buku['status'] > 0) {
                                                        $stock_class = 'stock-exist';
                                                    }
                                                    ?>
                                                    <span class="stock-badge <?= $stock_class ?>">
                                                        <?= $buku['status'] ?> 
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="ubahbuku.php?id=<?= $buku['id_buku'] ?>" class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <a href="hapusbuku.php?id=<?= $buku['id_buku'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus buku ini?')">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
<!-- category section -->
            <div class="row g-4 mb-4">
            <div class="col-12" id="kategori-section" data-aos="fade-up" data-aos-delay="100">
                <div class="table-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title mb-0">
                                <i class="bi bi-tags me-2"></i>Kategori Buku
                            </h4>
                            <a href="tambahkategori.php" class="btn btn-primary btn-custom">
                            <i class="ri-add-circle-line me-1"></i></i>Tambah Kategori
                            </a>
                        </div>
                        <div class="scrollable">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nama Kategori</th>
                                        <th>Jumlah Buku</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data_kategori as $kategori): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($kategori['nama_kategori']) ?></td>
                                            <td>
                                                <?php 
                                                $book_count = count(array_filter($data_buku, function($buku) use ($kategori) {
                                                    return $buku['id_kategori'] == $kategori['id_kategori'];
                                                }));
                                                echo $book_count;
                                                ?>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="ubahkategori.php?id=<?= $kategori['id_kategori'] ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a href="hapuskategori.php?id=<?= $kategori['id_kategori'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        
    </div>
    </div>
    <!-- Theme Toggle Button -->
    <!-- <button class="theme-toggle" id="themeToggle">
        <i class="ri-moon-line"></i>
    </button> -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true
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

        // Mobile Sidebar Toggle
        const sidebar = document.querySelector('.sidebar');
        const navbarToggler = document.querySelector('.navbar-toggler');

        navbarToggler.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(e.target) && !navbarToggler.contains(e.target)) {
                    sidebar.classList.remove('active');
                }
            }
        });

        // Active link handling
        const sidebarLinks = document.querySelectorAll('.sidebar-link');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', () => {
                sidebarLinks.forEach(l => l.classList.remove('active'));
                link.classList.add('active');
            });
        });
    </script>
</body>
</html>


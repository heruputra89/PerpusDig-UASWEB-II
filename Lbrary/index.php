<?php
require_once 'config/controller.php';
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
$data_akun = select("SELECT * FROM user");

// Debug login status
error_log("Login status - is_logged_in: " . ($is_logged_in ? 'true' : 'false'));
error_log("User role: " . $user_role);
error_log("User name: " . $user_name);

include 'config/koneksi.php';

// Get user profile data if logged in
$profile = [];
if ($is_logged_in) {
    $user_id = $_SESSION['user_id'];
    $query_profile = "SELECT * FROM user WHERE id_user = '$user_id'";
    $result_profile = mysqli_query($conn, $query_profile);
    if ($result_profile) {
        $profile = mysqli_fetch_assoc($result_profile);
    }
}

// Fetch book data
$query = "SELECT * FROM buku ORDER BY id_buku DESC LIMIT 6";
$data_buku = mysqli_query($conn, $query);

// Get total counts
$total_buku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM buku"))['total'];
$total_anggota = count(array_filter($data_akun, function($user) {
    return strtolower($user['status']) === 'aktif';
}));
$total_kategori = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM kategori"))['total'];
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Lbrary</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
    <!-- Swiper Modern Carousel -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <!-- GSAP for Modern Animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <!-- Modern CSS Variables and Styles -->
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

        /* Remove light theme styles */
        /* [data-theme="light"] {
            --bg-primary: #FFFFFF;
            --bg-secondary: #F8F9FA;
            --text-primary: #F0F0F0;
            --text-secondary: #4B5563;
            --accent-primary: #FFD700;
            --accent-secondary: #FFF8DC;
            --border-color: rgba(0, 0, 0, 0.1);
            --card-bg: #FFFFFF;
            --nav-bg: rgba(255, 255, 255, 0.8);
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.2);
            --glass-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.1);
        } */

        /* Base styles */
        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
            padding-top: 4rem; /* Increased padding for fixed navbar */
            scroll-padding-top: 4rem;
            line-height: 1.6;
            transition: all 0.3s ease;
        }

        /* Section spacing and layout */
        section {
            position: relative;
            padding: 5rem 0;
            margin: 0;
            z-index: 1;
        }

        /* Hero section specific */
        .hero {
            min-height: calc(100vh - 4rem);
            padding: 0;
            margin-top: -4rem; /* Compensate for body padding */
            position: relative;
            z-index: 2;
        }

        /* Navbar positioning */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            background: var(--nav-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
        }

        /* Section backgrounds */
        #search {
            background: var(--bg-secondary);
            position: relative;
            z-index: 1;
        }

        #quick-access {
            background: var(--bg-primary);
            position: relative;
            z-index: 1;
        }

        #tentang {
            background: var(--bg-secondary);
            position: relative;
            z-index: 1;
        }

        #katalog {
            background: var(--bg-primary);
            position: relative;
            z-index: 1;
        }

        #komentar {
            background: var(--bg-secondary);
            position: relative;
            z-index: 1;
        }

        #kontak {
            background: var(--bg-primary);
            position: relative;
            z-index: 1;
        }

        #event {
            background: var(--bg-secondary);
            position: relative;
            z-index: 1;
        }

        /* Container spacing */
        .max-w-7xl {
            padding: 0 2rem;
            margin: 0 auto;
            max-width: 1280px;
        }

        /* Card spacing */
        .card {
            margin-bottom: 2rem;
            position: relative;
            z-index: 1;
        }

        /* Feature section specific */
        .feature-content {
            position: relative;
            z-index: 2;
        }

        .feature-carousel {
            position: relative;
            z-index: 2;
        }

        /* Book grid spacing */
        .book-grid {
            margin: 2rem 0;
            position: relative;
            z-index: 1;
        }

        /* Comments section spacing */
        .comments {
            margin: 2rem 0;
            position: relative;
            z-index: 1;
        }

        /* Footer positioning */
        footer {
            position: relative;
            z-index: 1;
            margin-top: 0;
        }

        /* Mobile menu positioning */
        .mobile-menu {
            position: fixed;
            top: 0;
            right: -100%;
            width: 80%;
            height: 100vh;
            z-index: 2000;
            background: var(--bg-primary);
            transition: right 0.3s ease;
        }

        .mobile-menu.active {
            right: 0;
        }

        /* Theme toggle positioning */
        #themeToggle {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 1000;
        }

        /* Loading animation positioning */
        .loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            body {
                padding-top: 3.5rem;
            }

            section {
                padding: 3rem 0;
            }

            .max-w-7xl {
                padding: 0 1rem;
            }
        }

        @media (max-width: 480px) {
            body {
                padding-top: 3rem;
            }

            section {
                padding: 2rem 0;
            }
        }

        /* Modern Glassmorphism */
        .glass {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            box-shadow: var(--glass-shadow);
        }

        /* Modern Geometric Patterns */
        .geometric-bg {
            position: relative;
            overflow: hidden;
        }

        .geometric-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                linear-gradient(45deg, var(--accent-primary) 25%, transparent 25%),
                linear-gradient(-45deg, var(--accent-primary) 25%, transparent 25%),
                linear-gradient(45deg, transparent 75%, var(--accent-primary) 75%),
                linear-gradient(-45deg, transparent 75%, var(--accent-primary) 75%);
            background-size: 20px 20px;
            opacity: 0.05;
            z-index: 0;
        }

        

        /* Modern Buttons */
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
            color: white;
           
        }

        .btn-primary:hover {
            background: var(--accent-secondary);
            transform: translateY(-2px);
           
        }

        .btn-outline {
            border: 2px solid var(--accent-primary);
            color: var(--accent-primary);
            background: transparent;
        }

        .btn-outline:hover {
            background: var(--accent-primary);
            color: white;
            transform: translateY(-2px);
        }

        /* Modern Cards */
        .card {
            background: var(--card-bg);
            border-radius: 1rem;
            padding: 2rem;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .card::before {
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

        .card:hover::before {
            transform: scaleX(1);
        }

        /* Modern Hero Section */
        .hero {
            min-height: calc(100vh - 4rem);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
            overflow: hidden;
            background: linear-gradient(rgba(255, 255, 0, 0.5), rgba(50, 45, 50, 0.1)),
                        url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(10, 10, 10, 0.95), rgba(10, 10, 10, 0.85));
            z-index: 1;
        }

        .hero h1 {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .hero p {
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        .hero .btn {
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .hero .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        /* Modern Search Bar */
        .search-bar {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 1rem;
            padding: 0.75rem 1.5rem;
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            display: flex;
            gap: 1rem;
            align-items: center;
            transform: translateY(0);
            transition: all 0.3s ease;
        }

        .search-bar input {
            background: transparent;
            border: none;
            outline: none;
            color: var(--text-primary);
            width: 100%;
            font-size: 1rem;
        }

        .search-bar input::placeholder {
            color: var(--text-secondary);
        }

        /* Modern Book Grid */
        .book-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            padding: 2rem 0;
        }

        .book-card {
            background: var(--card-bg);
            border-radius: 1rem;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
            position: relative;
        }

        .book-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .book-cover {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }

        .book-info {
            padding: 1.5rem;
            position: relative;
            z-index: 1;
        }

        /* Modern Comments Section */
        .comments {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .comment {
            background: var(--card-bg);
            border-radius: 1rem;
            padding: 2rem;
            border: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
        }

        .comment::before {
            content: '"';
            position: absolute;
            top: -20px;
            left: -20px;
            font-size: 120px;
            color: var(--accent-primary);
            opacity: 0.1;
            font-family: serif;
        }

        .comment-author {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .comment-author img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Modern Footer */
        footer {
            background: var(--bg-secondary);
            padding: 4rem 0 2rem;
            border-top: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
        }

        footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(to right, transparent, var(--accent-primary), transparent);
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .social-links a {
            color: var(--text-secondary);
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            color: var(--accent-primary);
            transform: translateY(-3px);
        }

        /* navbar */
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

        /* Modern Animations */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        .float {
            animation: float 6s ease-in-out infinite;
        }

        /* Enhanced Card Hover Effects */
        .card {
            transform: translateY(0);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        /* Enhanced Button Styles */
        .btn {
            position: relative;
            overflow: hidden;
        }

        .btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn:hover::after {
            width: 300px;
            height: 300px;
        }

        /* Enhanced Search Bar */
        .search-bar:focus-within {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        /* Enhanced Book Cards */
        .book-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .book-card:hover::after {
            opacity: 1;
        }

        /* Enhanced Comments */
        .comment::before {
            content: '"';
            position: absolute;
            top: -20px;
            left: -20px;
            font-size: 120px;
            color: var(--accent-primary);
            opacity: 0.1;
            font-family: serif;
        }

        /* Enhanced Footer */
        footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(to right, transparent, var(--accent-primary), transparent);
        }

        /* Enhanced Mobile Menu */
        .mobile-menu {
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        /* Enhanced Theme Toggle */
        #themeToggle {
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        /* Loading Animation */
        .loading {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--bg-primary);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease;
        }

        .loading.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 3px solid var(--accent-primary);
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Feature Section Styles */
        .feature-text {
            opacity: 0;
            transform: translateX(-20px);
            transition: all 0.5s ease;
            display: none;
        }

        .feature-text.active {
            opacity: 1;
            transform: translateX(0);
            display: block;
        }

        .feature-carousel {
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
        }

        .feature-carousel .swiper {
            width: 100%;
            height: 100%;
        }

        .feature-carousel .swiper-slide {
            width: 100%;
            height: 400px;
            position: relative;
        }

        .feature-carousel .swiper-button-next,
        .feature-carousel .swiper-button-prev {
            color: var(--accent-primary);
            background: rgba(0, 0, 0, 0.5);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .feature-carousel .swiper-button-next:after,
        .feature-carousel .swiper-button-prev:after {
            font-size: 1.2rem;
        }

        .feature-carousel .swiper-button-next:hover,
        .feature-carousel .swiper-button-prev:hover {
            background: var(--accent-primary);
            color: #000;
        }

        .feature-carousel .swiper-pagination-bullet {
            background: var(--text-secondary);
            opacity: 0.5;
        }

        .feature-carousel .swiper-pagination-bullet-active {
            background: var(--accent-primary);
            opacity: 1;
        }

        /* Update section container styles */
        .section-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* Search Section */
        #search {
            padding: 4rem 0;
            background: linear-gradient(to bottom, var(--bg-secondary), var(--bg-primary));
        }

        #search .search-bar {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1rem;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }

        #search .search-bar:focus-within {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--accent-primary);
       
        }

        /* Quick Access Section */
        #quick-access {
            padding: 4rem 0;
            background: linear-gradient(to bottom, var(--bg-primary), var(--bg-secondary));
        }

        #quick-access .card {
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        #quick-access .card:hover {
            transform: translateY(-5px);
            background: rgba(34, 197, 94, 0.1);
        }

        /* How to Use Section */
        #tentang {
            padding: 4rem 0;
            background: linear-gradient(to bottom, var(--bg-secondary), var(--bg-primary));
        }

        #tentang .card {
            padding: 2.5rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        #tentang .card i {
            margin-bottom: 1.5rem;
        }

        /* Book Catalog Section */
        #katalog {
            padding: 4rem 0;
            background: linear-gradient(to bottom, var(--bg-primary), var(--bg-secondary));
        }

        .book-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            padding: 1rem 0;
        }

        .book-card {
            transform: translateY(0);
            transition: all 0.3s ease;
        }

        .book-card:hover {
            transform: translateY(-5px);
        }

        .book-info {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .btn-primary {
            background: var(--accent-primary);
            color: white;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--accent-secondary);
            transform: translateY(-2px);
        }

        .btn-outline {
            border: 2px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .btn-outline:hover {
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        /* Comments Section */
        #komentar {
            padding: 4rem 0;
            background: linear-gradient(to bottom, var(--bg-secondary), var(--bg-primary));
        }

        .comments {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .comment {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 1rem;
            padding: 2rem;
            transition: all 0.3s ease;
        }

        .comment:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.08);
        }

        .comment-author {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Contact Section */
        #kontak {
            padding: 4rem 0;
            background: linear-gradient(to bottom, var(--bg-primary), var(--bg-secondary));
        }

        #kontak .card {
            padding: 2.5rem;
            height: 100%;
        }

        #kontak .social-links {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body>
     <!-- Modern Navbar -->
     <nav class="navbar glass">
        <div class="container">
            <a class="navbar-brand" href="#">
                Lbrary
            </a>
            <!-- Mobile Menu Button -->
            <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="ri-menu-line"></i>
            </button>
            <!-- Desktop Navigation -->
            <div class="d-none d-md-flex align-items-center">
                <a href="index.php" class="nav-link active">Beranda</a>
                <a href="buku.php" class="nav-link ">Katalog Buku</a>
                
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
                        <a class="nav-link active" href="index.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="buku.php">Katalog Buku</a>
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
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <img src="<?= !empty($profile['foto']) ? 'uploads/profile/' . $profile['foto'] : 'assets/img/default-avatar.png' ?>" 
                                 alt="Profile" class="rounded-circle" width="35" height="35" style="object-fit: cover;">
                            <span class="d-none d-lg-inline"><?= htmlspecialchars($user_name ?? '') ?></span>
                        </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php if ($user_role === 'siswa'): ?>
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


    <style>
    /* Navbar Styles */
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
    </style>

    <script>
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

   

    <!-- Modern Hero Section -->
    <section id="beranda" class="hero relative overflow-hidden">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-gradient-to-br from-primary-900/50 to-dark-900/50"></div>
            <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80')] bg-cover bg-center bg-fixed opacity-20"></div>
            <!-- Animated Shapes -->
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden">
                <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-primary-500/10 rounded-full blur-3xl animate-float"></div>
                <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-primary-400/10 rounded-full blur-3xl animate-float-delayed"></div>
            </div>
        </div>

        <!-- Hero Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 min-h-screen flex items-center">
            <div class="text-center w-full">
                <!-- Animated Title -->
                <h1 class="text-5xl md:text-7xl font-bold mb-6 bg-clip-text text-transparent bg-gradient-to-r from-primary-400 to-primary-600 animate-gradient">
                    Selamat Datang di Lbrary
                </h1>
                
                <!-- Animated Subtitle -->
                <p class="text-xl md:text-2xl mb-12 text-gray-300 max-w-3xl mx-auto leading-relaxed">
                    Temukan ribuan buku digital dan fisik untuk menunjang pembelajaran Anda
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row justify-center gap-6 mb-12">
                    <a href="buku.php" class="btn btn-primary group">
                        <i class="ri-book-2-line mr-2 group-hover:rotate-12 transition-transform"></i>
                        Mulai Membaca
                    </a>
                    <a href="#fitur" class="btn btn-outline group">
                        <i class="ri-information-line mr-2 group-hover:rotate-12 transition-transform"></i>
                        Pelajari Lebih Lanjut
                    </a>
                </div>

                <!-- Stats Preview -->
                <div class="grid grid-cols-2 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                    <div class="stat-card">
                        <div class="text-3xl font-bold text-primary-500 mb-2" id="hero-stat-buku"><?php echo $total_buku; ?></div>
                        <div class="text-gray-400">Buku Tersedia</div>
                    </div>
                    <div class="stat-card">
                        <div class="text-3xl font-bold text-primary-500 mb-2" id="hero-stat-anggota"><?php echo $total_anggota; ?></div>
                        <div class="text-gray-400">Anggota Aktif</div>
                    </div>
                    <div class="stat-card">
                        <div class="text-3xl font-bold text-primary-500 mb-2" id="hero-stat-kategori"><?php echo $total_kategori; ?></div>
                        <div class="text-gray-400">Kategori</div>
                    </div>
                </div>

                <!-- Scroll Indicator -->
                <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
                    <a href="#fitur" class="text-gray-400 hover:text-primary-500 transition-colors">
                        <i class="ri-arrow-down-line text-3xl"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <style>
    /* Hero Section Styles */
    .hero {
        position: relative;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    /* Animated Background */
    .animate-float {
        animation: float 6s ease-in-out infinite;
    }

    .animate-float-delayed {
        animation: float 6s ease-in-out infinite;
        animation-delay: -3s;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0) scale(1); }
        50% { transform: translateY(-20px) scale(1.1); }
    }

    /* Gradient Text Animation */
    .animate-gradient {
        background-size: 200% 200%;
        animation: gradient 8s ease infinite;
    }

    @keyframes gradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* Stat Cards */
    .stat-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 1rem;
        padding: 1.5rem;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.2);
    }

    /* Enhanced Buttons */
    .btn {
        padding: 1rem 2rem;
        border-radius: 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .btn:hover::after {
        width: 300px;
        height: 300px;
    }

    .btn-primary {
        background-color: var(--accent-primary) !important;
        color: white;
        border: none;
    }

    .btn-outline {
        border: 2px solid var(--accent-primary);
        color: var(--accent-primary);
        background: transparent;
    }

    .btn-outline:hover {
        background: var(--accent-primary);
        color: white;
    }
    </style>

    <script>
    // Add this to your existing JavaScript
    document.addEventListener('DOMContentLoaded', function() {
        // Animate stats on scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    // Only animate if not already animated
                    if (!entry.target.classList.contains('animated')) {
                        const stats = entry.target.querySelectorAll('[id^="hero-stat-"]');
                        stats.forEach(stat => {
                            const endValue = parseInt(stat.textContent);
                            animateValue(stat.id, 0, endValue, 1500);
                        });
                        entry.target.classList.add('animated');
                    }
                }
            });
        });

        // Observe the hero section
        const heroSection = document.querySelector('.hero');
        if (heroSection) {
            observer.observe(heroSection);
        }

        function animateValue(id, start, end, duration) {
            let obj = document.getElementById(id);
            if (!obj) return;
            
            let range = end - start;
            let current = start;
            let increment = end > start ? 1 : -1;
            let stepTime = Math.abs(Math.floor(duration / range));
            
            // Clear any existing interval
            if (obj.animationInterval) {
                clearInterval(obj.animationInterval);
            }
            
            obj.animationInterval = setInterval(function() {
                current += increment;
                obj.textContent = current;
                if (current == end) {
                    clearInterval(obj.animationInterval);
                    delete obj.animationInterval;
                }
            }, stepTime);
        }
    });
    </script>

    <!-- Modern Features Section -->
    <section id="fitur" class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center mb-12">Fitur Unggulan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <!-- Feature Text Content -->
                <div class="feature-content">
                    <div class="feature-text active" data-slide="0">
                        <h3 class="text-2xl font-bold mb-4 text-primary-500">Pencarian Cepat</h3>
                        <p class="text-gray-400 mb-6">Temukan buku yang Anda cari dengan mudah menggunakan fitur pencarian canggih. Sistem kami menggunakan algoritma pencarian yang kuat untuk memberikan hasil yang relevan dan akurat.</p>
                        <ul class="space-y-3">
                            <li class="flex items-center space-x-2">
                                <i class="ri-check-line text-primary-500"></i>
                                <span>Pencarian berdasarkan judul, penulis, dan kategori</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <i class="ri-check-line text-primary-500"></i>
                                <span>Filter hasil pencarian yang fleksibel</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <i class="ri-check-line text-primary-500"></i>
                                <span>Hasil pencarian real-time</span>
                            </li>
                        </ul>
                    </div>
                    <div class="feature-text hidden" data-slide="1">
                        <h3 class="text-2xl font-bold mb-4 text-primary-500">Koleksi Digital</h3>
                        <p class="text-gray-400 mb-6">Akses ribuan buku digital kapan saja dan di mana saja. Koleksi digital kami terus diperbarui dengan buku-buku terbaru dan terpopuler.</p>
                        <ul class="space-y-3">
                            <li class="flex items-center space-x-2">
                                <i class="ri-check-line text-primary-500"></i>
                                <span>Format buku yang beragam (PDF, EPUB, MOBI)</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <i class="ri-check-line text-primary-500"></i>
                                <span>Baca offline dengan fitur download</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <i class="ri-check-line text-primary-500"></i>
                                <span>Koleksi buku terbaru setiap minggu</span>
                            </li>
                        </ul>
                    </div>
                    <div class="feature-text hidden" data-slide="2">
                        <h3 class="text-2xl font-bold mb-4 text-primary-500">Peminjaman Online</h3>
                        <p class="text-gray-400 mb-6">Pinjam buku secara online dan pantau status peminjaman Anda. Sistem peminjaman kami yang mudah digunakan membuat proses peminjaman menjadi lebih efisien.</p>
                        <ul class="space-y-3">
                            <li class="flex items-center space-x-2">
                                <i class="ri-check-line text-primary-500"></i>
                                <span>Peminjaman buku fisik dan digital</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <i class="ri-check-line text-primary-500"></i>
                                <span>Notifikasi pengembalian otomatis</span>
                            </li>
                            <li class="flex items-center space-x-2">
                                <i class="ri-check-line text-primary-500"></i>
                                <span>Riwayat peminjaman lengkap</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Feature Image Carousel -->
                <div class="feature-carousel relative">
                    <div class="swiper featureSwiper">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <img src="https://images.unsplash.com/photo-1507842217343-583bb7270b66?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80" 
                                     alt="Pencarian Cepat" 
                                     class="w-full h-[400px] object-cover rounded-lg shadow-lg" />
                            </div>
                            <div class="swiper-slide">
                                <img src="https://images.unsplash.com/photo-1512820790803-83ca734da794?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80" 
                                     alt="Koleksi Digital" 
                                     class="w-full h-[400px] object-cover rounded-lg shadow-lg" />
                            </div>
                            <div class="swiper-slide">
                                <img src="https://images.unsplash.com/photo-1507842217343-583bb7270b66?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80" 
                                     alt="Peminjaman Online" 
                                     class="w-full h-[400px] object-cover rounded-lg shadow-lg" />
                            </div>
                        </div>
                        <div class="swiper-pagination"></div>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Search Section -->
    <section id="search" class="py-16 bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center mb-8">Cari Buku Favorit Anda</h2>
            <form action="index.php" method="GET" class="max-w-3xl mx-auto">
                <div class="flex gap-4 bg-white/10 p-4 rounded-lg backdrop-blur-sm">
                    <input type="text" name="search" placeholder="Cari berdasarkan judul, penulis, atau kategori..." 
                           class="flex-1 bg-transparent border-none outline-none text-white placeholder-gray-400" />
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-search-line"></i>
                        Cari
                    </button>
                </div>
        </form>
        </div>
    </section>




    <!-- Book Catalog Section -->
    <section id="katalog" class="py-16 bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center mb-12 text-white">Buku Terbaik di Lbrary</h2>
            <div class="book-grid">
                <?php 
                // Fetch books with category and shelf information
                $query = "SELECT b.*, k.nama_kategori
                         FROM buku b 
                         LEFT JOIN kategori k ON b.id_kategori = k.id_kategori 
                      
                         ORDER BY b.judul ASC";
                $result = mysqli_query($conn, $query);
                
                if (mysqli_num_rows($result) > 0): 
                    while($buku = mysqli_fetch_assoc($result)): 
                ?>
                    <div class="book-card bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300" 
                         data-book='<?= json_encode([
                            "id" => $buku['id_buku'],
                            "judul" => $buku['judul'],
                            "penulis" => $buku['penulis'],
                            "penerbit" => $buku['penerbit'],
                            "tahun" => $buku['tahun'],
                            "kategori" => $buku['nama_kategori'],
                           
                            "gambar" => $buku['gambar'],
                            "pdf_file" => $buku['pdf_file']
                         ]) ?>'>
                        <img src="<?= !empty($buku['gambar']) ? 'uploads/books/' . $buku['gambar'] : 'https://via.placeholder.com/400x300?text=' . urlencode($buku['judul']) ?>" 
                             alt="<?= htmlspecialchars($buku['judul']) ?>" 
                             class="book-cover w-full h-64 object-cover" />
                        <div class="book-info p-6">
                            <span class="text-sm text-primary-400 font-medium"><?= htmlspecialchars($buku['nama_kategori']) ?></span>
                            <h3 class="text-xl font-bold mt-2 mb-1 text-white"><?= htmlspecialchars($buku['judul']) ?></h3>
                            <p class="text-gray-300 mb-2">Penulis: <?= htmlspecialchars($buku['penulis']) ?></p>
                            <p class="text-gray-300 mb-2">Penerbit: <?= htmlspecialchars($buku['penerbit']) ?></p>
                            <p class="text-gray-300 mb-2">Tahun: <?= htmlspecialchars($buku['tahun']) ?></p>
                            
                            <p class="text-gray-300 mb-4">Status: <?= htmlspecialchars($buku['status']) ?></p>
                            <button class="btn btn-primary w-full bg-primary-500 hover:bg-primary-600 text-black font-semibold py-2 px-4 rounded-lg transition-all duration-300 flex items-center justify-center gap-2 read-book-btn">
                                <i class="ri-eye-line"></i>
                                Lihat Detail
                            </button>
                        </div>
                    </div>
                <?php 
                    endwhile;
                else: 
                ?>
                    <div class="col-span-full text-center py-8">
                        <p class="text-gray-300">Belum ada buku yang tersedia.</p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="text-center mt-8">
                <a href="buku.php" class="btn btn-outline bg-white/10 hover:bg-white/20 text-white border-white/20 hover:border-white/30 font-semibold py-2 px-6 rounded-lg transition-all duration-300 inline-flex items-center gap-2">
                    <i class="ri-book-2-line"></i>
                    Lihat Semua Buku
                </a>
            </div>
        </div>
    </section>

    <!-- Section Event/Agenda Perpustakaan -->
    <section id="event" class="py-16 bg-gray-900/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center mb-12">Agenda & Event Terbaru</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="card">
                    <h3 class="font-bold text-lg mb-2">Bedah Buku: "Membaca Dunia"</h3>
                    <p class="text-gray-400 mb-2">Sabtu, 20 Juli 2024</p>
                    <p class="text-gray-400">Diskusi buku bersama penulis dan pembaca. <br><span class="text-primary-500">Gratis!</span></p>
                </div>
                <div class="card">
                    <h3 class="font-bold text-lg mb-2">Lomba Resensi Buku</h3>
                    <p class="text-gray-400 mb-2">Senin, 5 Agustus 2024</p>
                    <p class="text-gray-400">Ayo ikut lomba resensi buku dan menangkan hadiah menarik!</p>
                </div>
                <div class="card">
                    <h3 class="font-bold text-lg mb-2">Pelatihan Literasi Digital</h3>
                    <p class="text-gray-400 mb-2">Kamis, 15 Agustus 2024</p>
                    <p class="text-gray-400">Belajar literasi digital untuk pelajar dan guru.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Comments Section -->
    <section id="komentar" class="py-16 bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center mb-12">Apa Kata Mereka</h2>
            <div class="comments">
                <!-- Comment 1 -->
                <div class="comment">
                    <p class="text-lg mb-4">"Sistem perpustakaan yang sangat membantu dan mudah digunakan! Saya bisa menemukan buku yang saya cari dengan cepat."</p>
                    <div class="comment-author">
                        <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Sari Wulandari" />
                        <div>
                            <h4 class="font-bold">Sari Wulandari</h4>
                            <p class="text-gray-400">Guru Bahasa Indonesia</p>
                        </div>
                    </div>
                </div>

                <!-- Comment 2 -->
                <div class="comment">
                    <p class="text-lg mb-4">"Fitur peminjaman online sangat memudahkan proses belajar saya. Tidak perlu antri lagi untuk meminjam buku."</p>
                    <div class="comment-author">
                        <img src="https://randomuser.me/api/portraits/men/45.jpg" alt="Budi Santoso" />
                        <div>
                            <h4 class="font-bold">Budi Santoso</h4>
                            <p class="text-gray-400">Mahasiswa Teknik Informatika</p>
                        </div>
                    </div>
                </div>

                <!-- Comment 3 -->
                <div class="comment">
                    <p class="text-lg mb-4">"Desain modern dan interaktif membuat saya betah menggunakan aplikasi ini. Koleksi bukunya juga lengkap!"</p>
                    <div class="comment-author">
                        <img src="https://randomuser.me/api/portraits/women/12.jpg" alt="Dewi Lestari" />
                        <div>
                            <h4 class="font-bold">Dewi Lestari</h4>
                            <p class="text-gray-400">Pelajar SMA</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="kontak" class="py-16 bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center mb-12">Hubungi Kami</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="card bg-white/5">
                    <h3 class="text-xl font-bold mb-6">Informasi Kontak</h3>
                    <ul class="space-y-4">
                        <li class="flex items-center gap-3">
                            <i class="ri-map-pin-line text-2xl text-primary-500"></i>
                            <span>Jl. Aminah Syukur No.82, Sungai Pinang Luar, Kec. Samarinda Kota, Kota Samarinda, Kalimantan Timur 75113</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="ri-phone-line text-2xl text-primary-500"></i>
                            <span>+62 895-3595-00159</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="ri-mail-line text-2xl text-primary-500"></i>
                            <span>info@eperpustakaan.com</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="ri-whatsapp-line text-2xl text-primary-500"></i>
                            <span>+62 895-3595-00159</span>
                        </li>
                    </ul>
                    <div class="mt-8">
                        <h4 class="font-semibold mb-4">Media Sosial</h4>
                        <div class="flex gap-4">
                            <a href="#" class="text-2xl text-gray-400 hover:text-primary-500 transition">
                                <i class="ri-facebook-fill"></i>
                            </a>
                            <a href="#" class="text-2xl text-gray-400 hover:text-primary-500 transition">
                                <i class="ri-instagram-fill"></i>
                            </a>
                            <a href="#" class="text-2xl text-gray-400 hover:text-primary-500 transition">
                                <i class="ri-twitter-fill"></i>
                            </a>
                </div>
                    </div>
                </div>
                <div class="card bg-white/5">
                    <h3 class="text-xl font-bold mb-6">Jam Operasional</h3>
                    <ul class="space-y-4">
                        <li class="flex items-center justify-between">
                            <span class="flex items-center gap-3">
                                <i class="ri-calendar-line text-2xl text-primary-500"></i>
                                <span>Senin - Jumat</span>
                            </span>
                            <span class="text-gray-400">08:00 - 17:00</span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span class="flex items-center gap-3">
                                <i class="ri-calendar-line text-2xl text-primary-500"></i>
                                <span>Sabtu</span>
                            </span>
                            <span class="text-gray-400">09:00 - 15:00</span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span class="flex items-center gap-3">
                                <i class="ri-calendar-line text-2xl text-primary-500"></i>
                                <span>Minggu</span>
                            </span>
                            <span class="text-gray-400">Tutup</span>
                        </li>
                    </ul>
                    <div class="mt-8 p-4 bg-primary-500/10 rounded-lg">
                        <h4 class="font-semibold mb-2">Layanan Darurat</h4>
                        <p class="text-gray-400">Untuk bantuan darurat, silakan hubungi:</p>
                        <p class="text-primary-500 font-semibold mt-1">+62 895-3595-00159</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modern Footer -->
    <footer class="py-12 bg-gray-900/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">Lbrary</h3>
                    <p class="text-gray-400">Platform perpustakaan digital modern untuk memudahkan akses ke pengetahuan.</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Tautan Cepat</h4>
                    <ul class="space-y-2">
                        <li><a href="#beranda" class="text-gray-400 hover:text-primary-500 transition">Beranda</a></li>
                        <li><a href="#katalog" class="text-gray-400 hover:text-primary-500 transition">Katalog</a></li>
                        <li><a href="#fitur" class="text-gray-400 hover:text-primary-500 transition">Fitur</a></li>
                        <li><a href="#kontak" class="text-gray-400 hover:text-primary-500 transition">Kontak</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Layanan</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-primary-500 transition">Peminjaman Buku</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-primary-500 transition">Katalog Digital</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-primary-500 transition">Bantuan</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-primary-500 transition">FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-4">Ikuti Kami</h4>
                    <div class="social-links">
                        <a href="https://www.facebook.com/len.juan.2025" class="text-gray-400 hover:text-primary-500 transition" aria-label="Facebook">
                            <i class="ri-facebook-fill text-2xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-primary-500 transition" aria-label="Twitter">
                            <i class="ri-twitter-fill text-2xl"></i>
                        </a>
                        <a href="https://github.com/LneGe" class="text-gray-400 hover:text-primary-500 transition" aria-label="GitHub">
                            <i class="ri-github-fill text-2xl"></i>
                        </a>
                        <a href="https://www.instagram.com/glenn_ald_/" class="text-gray-400 hover:text-primary-500 transition" aria-label="Instagram">
                            <i class="ri-instagram-fill text-2xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-primary-500 transition" aria-label="YouTube">
                            <i class="ri-youtube-fill text-2xl"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center">
                <p class="text-gray-400">&copy; <?php echo date('Y'); ?> Lbrary. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Loading Animation -->
    <div class="loading">
        <div class="loading-spinner"></div>
    </div>

    <!-- Modern Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Single animation function with proper cleanup
        function animateCounter(element, start, end, duration) {
            if (!element || element.dataset.animated === 'true') return;
            
            let startTime = null;
            const range = end - start;
            
            function updateCounter(timestamp) {
                if (!startTime) startTime = timestamp;
                const progress = Math.min((timestamp - startTime) / duration, 1);
                const current = Math.floor(start + (range * progress));
                element.textContent = current;
                
                if (progress < 1) {
                    requestAnimationFrame(updateCounter);
                } else {
                    element.textContent = end;
                    element.dataset.animated = 'true';
                }
            }
            
            requestAnimationFrame(updateCounter);
        }

        // Initialize animations when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize counters
            const counters = {
                'hero-stat-buku': <?php echo $total_buku; ?>,
                'hero-stat-anggota': <?php echo $total_anggota; ?>,
                'hero-stat-kategori': <?php echo $total_kategori; ?>
            };

            // Create intersection observer for counters
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const container = entry.target;
                        Object.entries(counters).forEach(([id, endValue]) => {
                            const element = document.getElementById(id);
                            if (element) {
                                animateCounter(element, 0, endValue, 1500);
                            }
                        });
                        observer.unobserve(container);
                    }
                });
            }, {
                threshold: 0.5
            });

            // Observe the hero section
            const heroSection = document.querySelector('.hero');
            if (heroSection) {
                observer.observe(heroSection);
            }

            // Theme Toggle
            const themeToggle = document.getElementById('themeToggle');
            const html = document.documentElement;
            const icon = themeToggle?.querySelector('i');

            if (themeToggle && icon) {
                function setTheme(theme) {
                    html.setAttribute('data-theme', theme);
                    localStorage.setItem('theme', theme);
                    icon.className = theme === 'light' ? 'ri-sun-line' : 'ri-moon-line';
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
            }

            // Loading Animation
            window.addEventListener('load', () => {
                const loading = document.querySelector('.loading');
                if (loading) loading.classList.add('hidden');
            });

            // Smooth Scroll
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    const targetElement = document.querySelector(targetId);
                    
                    if (targetElement) {
                        const navHeight = document.querySelector('nav').offsetHeight;
                        const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset;
                        const offsetPosition = targetPosition - navHeight;

                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth'
                        });

                        // Close mobile menu if open
                        const mobileMenu = document.getElementById('mobileMenu');
                        if (mobileMenu?.classList.contains('active')) {
                            mobileMenu.classList.remove('active');
                            document.body.style.overflow = '';
                        }
                    }
                });
            });

            // Mobile Menu
            const mobileMenu = document.getElementById('mobileMenu');
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const closeMenuBtn = document.getElementById('closeMenuBtn');

            if (mobileMenu && mobileMenuBtn && closeMenuBtn) {
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
            }

            // Initialize Feature Swiper
            const featureSwiper = new Swiper('.featureSwiper', {
                slidesPerView: 1,
                spaceBetween: 30,
                loop: true,
                speed: 1000,
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                on: {
                    init: function() {
                        document.querySelectorAll('.feature-text').forEach(text => {
                            text.classList.remove('active');
                        });
                        document.querySelector('.feature-text[data-slide="0"]')?.classList.add('active');
                    },
                    slideChange: function() {
                        document.querySelectorAll('.feature-text').forEach(text => {
                            text.classList.remove('active');
                        });
                        const currentIndex = this.realIndex;
                        const currentText = document.querySelector(`.feature-text[data-slide="${currentIndex}"]`);
                        if (currentText) {
                            currentText.classList.add('active');
                        }
                    }
                }
            });
        });
    </script>
    <!-- Include Modals -->
    <?php include 'includes/modals.php'; ?>

    <script>
    // Add this to your existing JavaScript
    document.querySelectorAll('.read-book-btn').forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const bookCard = button.closest('.book-card');
            const bookData = JSON.parse(bookCard.dataset.book);
            
            if (bookData.pdf_file) {
                <?php if ($is_logged_in): ?>
                    Swal.fire({
                        title: 'Baca Buku?',
                        text: `Apakah Anda ingin membaca "${bookData.judul}"?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Baca',
                        cancelButtonText: 'Tidak',
                        background: '#1A1A1A',
                        color: '#FFFFFF',
                        confirmButtonColor: '#FFD700',
                        cancelButtonColor: '#FFF8DC'
                    }).then((result) => {
                        if (result.isConfirmed) {
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
                        }
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
        });
    });
    </script>
</body>
</html>


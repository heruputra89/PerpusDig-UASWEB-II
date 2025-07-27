<?php
include 'config/controller.php';
include 'config/session_check.php';

// Check if user is admin or petugas
$role = getCurrentRole();
if (!in_array($role, ['admin', 'petugas'])) {
    header("Location: " . getDashboardUrl());
    exit();
}

$showAlert = false;
$alertType = '';
$alertMessage = '';

// Get all categories and shelves for dropdowns
$kategori = select("SELECT * FROM kategori ORDER BY nama_kategori ASC");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $penulis = $_POST['penulis'];
    $tahun = $_POST['tahun'];
    $penerbit = $_POST['penerbit'];
    $id_kategori = $_POST['id_kategori'];

    
    // Handle image upload
    $gambar = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "uploads/books/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Check if image file is actual image
        $check = getimagesize($_FILES["gambar"]["tmp_name"]);
        if($check !== false) {
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                $gambar = $new_filename;
            }
        }
    }

    // Handle PDF upload
    $pdf_file = '';
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == 0) {
        $target_dir = "uploads/books/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["pdf_file"]["name"], PATHINFO_EXTENSION));
        if ($file_extension == 'pdf') {
            $new_filename = uniqid() . '.pdf';
            $target_file = $target_dir . $new_filename;
            
            if (move_uploaded_file($_FILES["pdf_file"]["tmp_name"], $target_file)) {
                $pdf_file = $new_filename;
            }
        }
    }

    $query = "INSERT INTO buku (judul, deskripsi, penulis, tahun, penerbit, status, id_kategori, gambar, pdf_file) 
              VALUES ('$judul', '$deskripsi', '$penulis', '$tahun', '$penerbit', '', '$id_kategori', '$gambar', '$pdf_file')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $showAlert = true;
        $alertType = 'success';
        $alertMessage = 'Data buku berhasil ditambahkan!';
    } else {
        $showAlert = true;
        $alertType = 'error';
        $alertMessage = 'Terjadi kesalahan saat menambahkan buku!';
    }
}
?>

<!DOCTYPE html>
<html lang="id" class="scroll-smooth" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Buku | Lbrary</title>
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
            --scrollbar-track: #1A1A1A;
            --scrollbar-thumb: #333333;
            --scrollbar-thumb-hover: #444444;
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

        /* Firefox Scrollbar */
        * {
            scrollbar-width: thin;
            scrollbar-color: var(--scrollbar-thumb) var(--scrollbar-track);
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

        .preview-image {
            max-width: 200px;
            max-height: 200px;
            object-fit: cover;
            border-radius: 0.75rem;
            display: none;
            margin-top: 1rem;
            border: 1px solid var(--border-color);
        }

        /* Modern Form Elements */
        input[type="file"] {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 0.75rem;
            color: var(--text-primary);
            width: 100%;
        }

        input[type="file"]::-webkit-file-upload-button {
            background: var(--accent-orange);
            color: black;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        input[type="file"]::-webkit-file-upload-button:hover {
            background: var(--accent-secondary);
        }

        select option {
            background: var(--bg-secondary);
            color: var(--text-primary);
        }
    </style>
</head>
<body>
    <div class="form-container">
        <a href="<?= getDashboardUrl() ?>" class="back-link">
            <i class="ri-arrow-left-line mr-2"></i>Kembali ke Dashboard
        </a>

        <div class="form-card">
            <div class="form-header">
                <h1 class="form-title">
                    <i class="ri-book-2-line mr-2"></i>Tambah Buku Baru
                </h1>
                <hr><br>
            </div>

            <form method="post" enctype="multipart/form-data">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="judul" class="form-label">Judul Buku</label>
                        <input type="text" class="form-control w-full" id="judul" name="judul" required>
                    </div>

                    <div>
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <input type="text" class="form-control w-full" id="deskripsi" name="deskripsi">
                    </div>

                    <div>
                        <label for="penulis" class="form-label">Penulis</label>
                        <input type="text" class="form-control w-full" id="penulis" name="penulis" required>
                    </div>

                    <div>
                        <label for="penerbit" class="form-label">Penerbit</label>
                        <input type="text" class="form-control w-full" id="penerbit" name="penerbit" required>
                    </div>

                    <div>
                        <label for="tahun" class="form-label">Tahun Terbit</label>
                        <input type="number" class="form-control w-full" id="tahun" name="tahun" required>
                    </div>

                    <div>
                        <label for="id_kategori" class="form-label">Kategori</label>
                        <select class="form-select w-full" id="id_kategori" name="id_kategori" required>
                            <option value="">Pilih Kategori</option>
                            <?php foreach ($kategori as $kat): ?>
                                <option value="<?= $kat['id_kategori'] ?>"><?= htmlspecialchars($kat['nama_kategori']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                


                    <div class="md:col-span-2">
                        <label for="gambar" class="form-label">Gambar Buku</label>
                        <input type="file" class="w-full" id="gambar" name="gambar" accept="image/*" onchange="previewImage(this)">
                        <img id="preview" class="preview-image" src="#" alt="Preview">
                    </div>

                    <div class="md:col-span-2">
                        <label for="pdf_file" class="form-label">File PDF Buku</label>
                        <input type="file" class="w-full" id="pdf_file" name="pdf_file" accept=".pdf">
                        <small class="text-gray-400">Upload file PDF buku (opsional)</small>
                    </div>
                </div>

                <div class="flex gap-4 mt-8">
                    <button type="submit" name="submit" class="btn btn-primary">
                        <i class="ri-check-line"></i>
                        Simpan
                    </button>
                    <a href="<?= getDashboardUrl() ?>" class="btn btn-secondary">
                        <i class="ri-close-line"></i>
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        <?php if ($showAlert): ?>
        Swal.fire({
            icon: '<?= $alertType ?>',
            title: '<?= $alertType == "success" ? "Berhasil!" : "Gagal!" ?>',
            text: '<?= $alertMessage ?>',
            showConfirmButton: false,
            timer: 2000,
            background: '#1A1A1A',
            color: '#FFFFFF'
        }).then(() => {
            window.location.href = '<?= $alertType == "success" ? "buku.php" : "tambahbuku.php" ?>';
        });
        <?php endif; ?>
    </script>
</body>
</html>

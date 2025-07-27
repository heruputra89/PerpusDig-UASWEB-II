<?php
// Konfigurasi database
$host = 'localhost'; // Ganti dengan alamat host database Anda
$dbname = 'perpustakaan'; // Ganti dengan nama database Anda
$username = 'root'; // Ganti dengan username database Anda
$password = ''; // Ganti dengan password database Anda

// Membuat koneksi
$conn = new mysqli($host, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8");

// Pastikan sesi dimulai dan siap digunakan
// Misalnya, menyimpan ID pengguna dalam sesi setelah login
if (isset($_SESSION['user_id'])) {
    // Lakukan sesuatu dengan ID pengguna jika diperlukan
    // Contoh: Mengambil data pengguna dari database
    $user_id = $_SESSION['user_id'];
}

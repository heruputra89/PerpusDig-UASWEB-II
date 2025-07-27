<?php
session_start();
include 'koneksi.php'; // atau sesuaikan dengan file koneksi kamu

if (!isset($_SESSION['level'])) {
    // kalau belum login, redirect ke index
    header("Location: index.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Proses hapus kategori
    $query = "DELETE FROM kategori WHERE id_kategori = '$id'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        // Hapus berhasil
        if ($_SESSION['level'] === 'admin') {
            header("Location: dashboard_admin.php");
            exit;
        } elseif ($_SESSION['level'] === 'petugas') {
            header("Location: dashboard_petugas.php");
            exit;
        } else {
            // fallback kalau level tidak diketahui
            header("Location: index.php");
            exit;
        }
    } else {
        echo "Gagal menghapus kategori: " . mysqli_error($conn);
    }
} else {
    echo "ID kategori tidak ditemukan!";
}
?>

<?php
include 'config/controller.php';
// menerima id produk yang dipilih untuk dihapus 
$id = (int)$_GET['id'];

// Kondisi ketika tombol hapus diklick
if (delete_akun($id, $conn) > 0){
    echo "<script>alert('Data Gagal Dihapus');document.location.href='dashboard_admin.php';</script>";
    exit;
} else {
    echo "<script>alert('Data Berhasil Dihapus');document.location.href='dashboard_admin.php';</script>";
    exit;
}
// No HTML output needed, as the script always redirects.
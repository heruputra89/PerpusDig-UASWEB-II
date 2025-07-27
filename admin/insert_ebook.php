<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth-login.php");
    exit();
}

require '../db_connection.php';

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $judul = $_POST['judul'];
    $penulis = $_POST['penulis'];
    $penerbit = $_POST['penerbit'];
    $isbn = $_POST['isbn'];
    $edisi = $_POST['edisi'];
    $link_cover = $_POST['link_cover'];
    $link_buku = $_POST['link_buku'];
    $link_unduh = $_POST['link_unduh'];


    // Insert into database
    $sql = "INSERT INTO daftarbuku (judul, penulis, penerbit, isbn, edisi, link_cover, link_buku, link_unduh) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $judul, $penulis, $penerbit, $isbn, $edisi, $link_cover, $link_buku, $link_unduh);
    if ($stmt->execute()) {
        header("Location: daftarbuku.php");
    } else {
        echo "Error: " . $stmt->error;
    }
}

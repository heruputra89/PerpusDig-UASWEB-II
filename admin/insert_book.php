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
    $barcode = $_POST['barcode'];


    // Insert into database
    $sql = "INSERT INTO buku (judul, penulis, penerbit, isbn, edisi, barcode) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $judul, $penulis, $penerbit, $isbn, $edisi, $barcode);
    if ($stmt->execute()) {
        header("Location: buku.php");
    } else {
        echo "Error: " . $stmt->error;
    }
}

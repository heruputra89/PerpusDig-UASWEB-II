<?php
    $conn = mysqli_connect("localhost", "root", "", "Lbrary_db");

    if (!$conn) {
        die("Koneksi gagal: " . mysqli_connect_error());
    }
?>

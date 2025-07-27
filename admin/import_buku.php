<?php
// Include composer autoload
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

// Koneksi ke database
$host = 'localhost';
$db = 'perpustakaan';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file']['tmp_name'];

    // Load the Excel file
    $spreadsheet = IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();

    // Loop through the rows
    foreach ($sheet->getRowIterator() as $row) {
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);

        $data = [];
        foreach ($cellIterator as $cell) {
            $data[] = $cell->getValue();
        }

        // Pastikan kolom di Excel sesuai dengan urutan nama, email, password
        $barcode = $data[0];
        $judul = $data[1];
        $penerbit = $data[2];
        $isbn = $data[3];
        $edisi = $data[4];
        $penulis = $data[5];

        // Insert data ke database
        $stmt = $conn->prepare("INSERT INTO buku (barcode, judul, penerbit, isbn, edisi, penulis) VALUES (?, ?, ?, ?, ?, ?)");

        if ($stmt === false) {
            die('Prepare failed: ' . $conn->error);
        }

        $stmt->bind_param("ssssss", $barcode, $judul, $penerbit, $isbn, $edisi, $penulis);

        if (!$stmt->execute()) {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        } else {
            echo "Judul $judul berhasil ditambahkan!<br>";
        }

        $stmt->close(); // Tutup statement setelah digunakan
    }
} else {
    echo "Upload file Excel terlebih dahulu.";
}

$conn->close(); // Tutup koneksi database setelah selesai
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload BukuExcel</title>
</head>

<body>
    <h2>Upload Buku Excel</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="file">Pilih file Excel:</label>
        <input type="file" name="file" id="file" required>
        <button type="submit">Upload</button>
    </form>
</body>

</html>
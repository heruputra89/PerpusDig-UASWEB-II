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

// Fungsi untuk hashing password
function hash_password($password)
{
    return password_hash($password, PASSWORD_DEFAULT);
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
        $name = $data[0];
        $email = $data[1];
        $password = $data[2];

        // Hash password sebelum dimasukkan ke database
        $hashed_password = hash_password($password);

        // Insert data ke database
        $stmt = $conn->prepare("INSERT INTO users (nama, email, password_hash) VALUES (?, ?, ?)");

        if ($stmt === false) {
            die('Prepare failed: ' . $conn->error);
        }

        $stmt->bind_param("sss", $name, $email, $hashed_password);

        if (!$stmt->execute()) {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        } else {
            echo "User $name berhasil ditambahkan!<br>";
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
    <title>Upload Users Excel</title>
</head>

<body>
    <h2>Upload Users Excel</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="file">Pilih file Excel:</label>
        <input type="file" name="file" id="file" required>
        <button type="submit">Upload</button>
    </form>
</body>

</html>
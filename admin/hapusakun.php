<?php
session_start();
include "../db_connection.php";

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Mendapatkan ID pengguna dari sesi
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Memeriksa apakah checkbox diaktifkan
    if (isset($_POST['accountActivation']) && $_POST['accountActivation'] == 'on') {
        // Menghapus data pengguna dari database
        $sqlDelete = "DELETE FROM users WHERE id = ?";
        $stmtDelete = $conn->prepare($sqlDelete);

        if ($stmtDelete) {
            $stmtDelete->bind_param('i', $user_id);

            if ($stmtDelete->execute()) {
                $_SESSION['success'] = "Account successfully deleted.";
                session_destroy(); // Menghentikan sesi pengguna
                header("Location: ../auth-login.php"); // Redirect ke halaman login
                exit();
            } else {
                $_SESSION['error'] = "Error: " . $stmtDelete->error;
            }

            $stmtDelete->close();
        } else {
            $_SESSION['error'] = "Error preparing statement for account deletion.";
        }
    } else {
        $_SESSION['error'] = "Please confirm account deactivation by checking the checkbox.";
    }

    $conn->close();
    header("Location: akun.php"); // Redirect kembali ke halaman akun untuk menampilkan pesan
    exit();
}
?>

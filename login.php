<?php
session_start();
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validasi input
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'Email dan Password harus diisi';
        header("Location: auth-login.php");
        exit;
    }

    // Cek user di database
    $stmt = $conn->prepare("SELECT id, nama, email, password_hash, role FROM users WHERE email = ?");

    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Verifikasi password
        if (password_verify($password, $user['password_hash'])) {
            // Simpan data pengguna dalam sesi
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Redirect berdasarkan peran
            switch ($user['role']) {
                case 'admin':
                    header("Location: admin");
                    break;
                case 'user':
                    header("Location: user");
                    break;
                default:
                    header("Location: guest");
                    break;
            }
            exit;
        } else {
            $_SESSION['error'] = 'Password salah';
            header("Location: auth-login.php");
            exit;
        }
    } else {
        $_SESSION['error'] = 'Email tidak ditemukan';
        header("Location: auth-login.php");
        exit;
    }

    $stmt->close();
    $conn->close();
}

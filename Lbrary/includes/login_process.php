<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set header to return JSON
header('Content-Type: application/json');

// Log the login attempt
error_log("Login attempt - POST data: " . print_r($_POST, true));

try {
    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get and validate username and password
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        throw new Exception('Username dan password harus diisi');
    }

    // Include database connection
    require_once __DIR__ . '/../config/koneksi.php';

    // Log database connection status
    error_log("Database connection status: " . ($conn ? "Connected" : "Failed"));

    // Prepare and execute query using prepared statement
    $query = "SELECT * FROM user WHERE username = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        error_log("Prepare statement error: " . $conn->error);
        throw new Exception('Database error: ' . $conn->error);
    }

    $stmt->bind_param("s", $username);
    
    if (!$stmt->execute()) {
        error_log("Execute statement error: " . $stmt->error);
        throw new Exception('Database error: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();

    // Log query result
    error_log("Query result rows: " . $result->num_rows);

    if ($result->num_rows === 0) {
        throw new Exception('Username tidak ditemukan');
    }

    $user = $result->fetch_assoc();

    // Log user data (excluding password)
    error_log("User data found: " . print_r(array_diff_key($user, ['password' => '']), true));

    // Check if account is active
    if (!isset($user['status']) || strtolower($user['status']) !== 'aktif') {
        error_log("Account status: " . ($user['status'] ?? 'not set'));
        throw new Exception('Akun tidak aktif');
    }

    // Verify password
    if ($password !== $user['password']) {
        error_log("Password mismatch for user: " . $username);
        throw new Exception('Password salah');
    }

    // Set session variables
    $_SESSION['user_id'] = $user['id_user'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['name'] = $user['nama'];
    $_SESSION['role'] = 'user'; // Set all non-admin roles to 'user'
    $_SESSION['logged_in'] = true;

    // Log successful login
    error_log("Login successful for user: " . $username . " with role: " . $user['level']);

    // Determine redirect URL based on role
    $redirect = '';
    if (strtolower($user['level']) === 'admin') {
        $redirect = 'dashboard_admin.php';
    } else {
        $redirect = 'dashboard_user.php'; // All other roles go to user dashboard
    }

    // Log redirect URL
    error_log("Redirect URL: " . $redirect);

    // Return success response
    echo json_encode([
        'status' => 'success',
        'message' => 'Login berhasil',
        'redirect' => $redirect
    ]);

} catch (Exception $e) {
    // Log the error
    error_log("Login error: " . $e->getMessage());
    
    // Return error response
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

// Close database connection
if (isset($stmt)) {
    $stmt->close();
}
if (isset($conn)) {
    $conn->close();
}
?> 
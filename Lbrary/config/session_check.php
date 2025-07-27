<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug session state
error_log("Session state in session_check.php: " . print_r($_SESSION, true));

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    error_log("Session check failed - user_id or role not set");
    header("Location: index.php");
    exit();
}

// Function to check if user has required role
function checkRole($requiredRole) {
    error_log("Checking role. Required: " . $requiredRole . ", Current: " . (isset($_SESSION['role']) ? $_SESSION['role'] : 'not set'));
    
    if (!isset($_SESSION['role'])) {
        error_log("No role in session");
        header("Location: index.php");
        exit();
    }
    
    // Check for specific role
    if ($requiredRole === 'admin' && $_SESSION['role'] !== 'admin') {
        error_log("Role mismatch. Required admin but got: " . $_SESSION['role']);
        header("Location: dashboard_" . $_SESSION['role'] . ".php");
        exit();
    }
}

// Function to get current user's role
function getCurrentRole() {
    return isset($_SESSION['role']) ? $_SESSION['role'] : null;
}

// Function to get current user's name
function getCurrentUserName() {
    return isset($_SESSION['name']) ? $_SESSION['name'] : 'User';
}

// Function to get appropriate dashboard URL based on role
function getDashboardUrl() {
    $role = getCurrentRole();
    switch ($role) {
        case 'admin':
            return 'dashboard_admin.php';
        case 'petugas':
            return 'dashboard_petugas.php';
        case 'user':
            return 'dashboard_user.php';
        default:
            return 'index.php';
    }
}

// Function to logout
function logout() {
    session_destroy();
    header("Location: index.php");
    exit();
}
?> 
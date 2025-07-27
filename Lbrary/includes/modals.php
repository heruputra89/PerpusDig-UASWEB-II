<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug session
error_log("Session contents: " . print_r($_SESSION, true));

// Update the include path to use the correct relative path
include __DIR__ . '/../config/koneksi.php';

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    // LOGIN LOGIC
    if (isset($_POST['action']) && $_POST['action'] === 'login') {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        
        try {
            if (empty($username) || empty($password)) {
                throw new Exception('Username dan password harus diisi');
            }

            // Check user in database
            $query = "SELECT * FROM user WHERE username = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 's', $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($user = mysqli_fetch_assoc($result)) {
                // Verify password
                if ($password === $user['password']) {
                    // Check if account is active
                    if (strtolower($user['status']) === 'aktif') {
                        // Set session variables
                        $_SESSION['user_id'] = $user['id_user'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['role'] = $user['level'];
                        $_SESSION['name'] = $user['name'];
                        $_SESSION['logged_in'] = true;

                        echo json_encode([
                            'status' => 'success',
                            'message' => 'Login berhasil'
                        ]);
                    } else {
                        throw new Exception('Akun tidak aktif');
                    }
                } else {
                    throw new Exception('Password salah');
                }
            } else {
                throw new Exception('Username tidak ditemukan');
            }
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    // REGISTER LOGIC
    if (isset($_POST['tambah'])) {
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $level = isset($_POST['level']) ? $_POST['level'] : '';
        $status = isset($_POST['status']) ? $_POST['status'] : '';

        // Check if username exists
        $check_query = "SELECT * FROM user WHERE username = ?";
        $check_stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($check_stmt, 's', $username);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_num_rows($check_result) > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Username sudah digunakan!']);
        } else {
            $query = "INSERT INTO user (email, name, username, password, level, status) VALUES (?, ?, ?, ?, 'user', 'aktif')";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, '', $name, $username, $password, $level, $status);
            
            if (mysqli_stmt_execute($stmt)) {
                echo json_encode(['status' => 'success', 'message' => 'Registrasi berhasil! Silakan login.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Registrasi gagal! Silakan coba lagi.']);
            }
        }
        exit;
    }
}
?>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-gray-800 text-white" style="display: flex; flex-direction: row; max-width: 700px;">
            <!-- Left side - Image -->
            <div class="login-image" style="flex: 0.8; background: url('https://i.pinimg.com/736x/57/f6/7a/57f67a7dbfaf500e3398a9395207821c.jpg') center/cover no-repeat; min-height: 350px; position: relative;">
                <div class="login-image::before" style="content: ''; position: absolute; inset: 0; background: rgba(44, 62, 80, 0.7);"></div>
                <div class="login-image-content" style="position: relative; z-index: 10; color: var(--text-color); padding: 1.5rem; height: 100%; display: flex; flex-direction: column; justify-content: center;">
                    <!-- <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem;">Selamat Datang!</h2>
                    <p style="font-size: 0.9rem; opacity: 0.9;">Masuk ke akun Anda untuk mengakses perpustakaan digital kami.</p> -->
                </div>
            </div>

            <!-- Right side - Form -->
            <div class="login-form" style="flex: 1; padding: 1.25rem; color: var(--text-color); max-width: 350px; margin: 0 auto;">
                <div class="login-header" style="text-align: center; margin-bottom: 1.25rem;">
                    <h1 class="login-title" style="font-size: 1.35rem; font-weight: 700; color: var(--accent-color); margin-bottom: 0.4rem;">
                        <i class="lni lni-book me-2"></i>Lbrary
                    </h1>
                    <p class="login-subtitle" style="color: var(--text-color); opacity: 0.8; font-size: 0.85rem;">Masuk ke akun Anda</p>
                </div>

                <form id="loginForm" class="space-y-4">
                    <!-- Error Alert -->
                    <div id="loginError" class="alert alert-danger d-none" style="border-radius: 8px; border: none; background-color: #4b4b4b; color: var(--accent-color); margin-bottom: 0.7rem; padding: 0.7rem; font-size: 0.85rem;">
                        <i class="ri-error-warning-line me-2"></i>
                        <span id="errorMessage"></span>
                    </div>

                    <div class="form-group">
                        <label for="username" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Username</label>
                        <input type="text" class="form-control" id="username" name="username" style="width: 100%; padding: 0.45rem 0.9rem; border-radius: 0.5rem; border: none; outline: none; font-size: 0.85rem; margin-bottom: 0.7rem; background-color: #444; color: var(--text-color); transition: background-color 0.3s ease;" required>
                    </div>

                    <div class="form-group">
                        <label for="password" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Password</label>
                        <div class="relative">
                            <input type="password" class="form-control" id="password" name="password" style="width: 100%; padding: 0.45rem 0.9rem; border-radius: 0.5rem; border: none; outline: none; font-size: 0.85rem; margin-bottom: 0.7rem; background-color: #444; color: var(--text-color); transition: background-color 0.3s ease;" required>
                            <div class="form-check" style="display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.7rem; font-size: 0.85rem;">
                                <input type="checkbox" id="showPassword" class="form-check-input" style="margin: 0;">
                                <label for="showPassword" class="form-check-label" style="user-select: none; color: var(--text-color); margin: 0;">Tampilkan Password</label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-full" style="background-color: var(--accent-color); color: var(--bg-color); padding: 0.45rem 1.25rem; font-weight: 600; border-radius: 9999px; width: 100%; transition: background-color 0.3s ease; margin-top: 0.4rem; font-size: 0.9rem;">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span class="button-text">Login</span>
                    </button>

                    <p class="mt-4 text-center text-gray-400">
                        Belum punya akun? 
                        <a href="#" class="text-primary-500 hover:text-primary-400" data-bs-toggle="modal" data-bs-target="#registerModal" data-bs-dismiss="modal">
                            Daftar di sini
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Register Modal -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-gray-800 border border-gray-700">
            <div class="modal-header border-b border-gray-700">
                <h5 class="modal-title text-xl font-bold text-primary-500" id="registerModalLabel">
                    <i class="ri-user-add-line me-2"></i>Daftar Akun
                </h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-6">
                <form action="" method="POST" novalidate>
                <div class="grid grid-cols-1 md:grid-cols-1 gap-4 mb-4">
                    <div>
                            <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                            <input type="email" id="email" name="email" 
                                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent" 
                                   placeholder="Masukkan email" required />
                        </div>
</div>
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-4 mb-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Nama Lengkap</label>
                            <input type="text" id="name" name="name" 
                                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent" 
                                   placeholder="Masukkan nama lengkap" required />
                        </div>
                        
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-4 mb-4">
                    <div>
                            <label for="username" class="block text-sm font-medium text-gray-300 mb-2">Username</label>
                            <input type="text" id="username" name="username" 
                                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent" 
                                   placeholder="Masukkan username" required />
                        </div>
</div>

                    <div class="grid grid-cols-1 md:grid-cols-1 gap-4 mb-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-300 mb-2">Password</label>
                            <input type="password" id="password" name="password" 
                                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent" 
                                   placeholder="Masukkan password" required />
                        </div>
                        

                    <button type="submit" name="tambah" 
                            class="w-full bg-primary-500 hover:bg-primary-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-300">
                        <i class="ri-user-add-line me-2"></i>Daftar
                    </button>

                    <p class="mt-4 text-center text-gray-400">
                        Sudah punya akun? 
                        <a href="#" class="text-primary-500 hover:text-primary-400" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal">
                            Login di sini
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Modal Styles */
.modal-content {
    background: rgba(31, 41, 55, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.modal-header {
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.form-control {
    background-color: rgba(55, 65, 81, 0.5);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: white;
}

.form-control:focus {
    background-color: rgba(55, 65, 81, 0.7);
    border-color: var(--accent-primary);
    color: white;
    box-shadow: 0 0 0 0.2rem rgba(255, 215, 0, 0.25);
}

.alert-danger {
    background-color: rgba(220, 53, 69, 0.1);
    border: 1px solid rgba(220, 53, 69, 0.2);
    color: #ff6b6b;
}

.btn-primary {
    background-color: var(--accent-primary);
    border: none;
    color: black;
}

.btn-primary:hover {
    background-color: var(--accent-secondary);
    transform: translateY(-1px);
}

.btn-primary:disabled {
    background-color: var(--accent-primary);
    opacity: 0.7;
}

/* Spinner Styles */
.spinner-border {
    width: 1rem;
    height: 1rem;
    border-width: 0.2em;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const loginError = document.getElementById('loginError');
    const errorMessage = document.getElementById('errorMessage');
    const submitButton = loginForm.querySelector('button[type="submit"]');
    const spinner = submitButton.querySelector('.spinner-border');
    const buttonText = submitButton.querySelector('.button-text');

    // Show/Hide Password
    const showPasswordCheckbox = document.getElementById('showPassword');
    const passwordInput = document.getElementById('password');
    
    showPasswordCheckbox.addEventListener('change', function() {
        passwordInput.type = this.checked ? 'text' : 'password';
    });

    // Handle form submission
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Reset error state
        loginError.classList.add('d-none');
        errorMessage.textContent = '';
        
        // Show loading state
        spinner.classList.remove('d-none');
        buttonText.textContent = 'Logging in...';
        submitButton.disabled = true;

        // Get form data
        const formData = new FormData(this);
        formData.append('action', 'login');
        
        // Send login request
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
                modal.hide();
                
                // Reload page to reflect new session state
                window.location.reload();
            } else {
                // Show error message
                errorMessage.textContent = data.message;
                loginError.classList.remove('d-none');
                
                // Reset button state
                spinner.classList.add('d-none');
                buttonText.textContent = 'Login';
                submitButton.disabled = false;
            }
        })
        /* .catch(error => {
            console.error('Login error:', error);
            
            // Show error message
            errorMessage.textContent = 'Password atau Username salah!';
            loginError.classList.remove('d-none');
            
            // Reset button state
            spinner.classList.add('d-none');
            buttonText.textContent = 'Login';
            submitButton.disabled = false;
        }); */
    });

    // Reset form when modal is closed
    document.getElementById('loginModal').addEventListener('hidden.bs.modal', function () {
        loginForm.reset();
        loginError.classList.add('d-none');
        errorMessage.textContent = '';
        spinner.classList.add('d-none');
        buttonText.textContent = 'Login';
        submitButton.disabled = false;
    });
});


</script>

<script src="https://cdn.tailwindcss.com"></script> 
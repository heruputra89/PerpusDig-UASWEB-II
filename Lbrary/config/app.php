<?php

//panggil koneksi data (koneksi.php)
include "koneksi.php";

function select($query) {
    global $conn; // Pastikan koneksi ke database sudah terhubung dengan variabel $conn
    $result = mysqli_query($conn, $query);

    // Periksa apakah query berhasil dieksekusi
    if (!$result) {
        die("Query Error: " . mysqli_error($conn));
    }

    // Mengambil data dalam bentuk array
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    return $data;
}
    
//fungsi untuk menambahkan akun
function tambah_akun($post) {
    global $conn;
    
    // Validate required fields
    if (!isset($post['name']) || !isset($post['username']) || !isset($post['password']) || !isset($post['level'])) {
        return false;
    }

    // Sanitize input
    $name = isset($post['name']) ? strip_tags($post['name']) : '';
    $username = isset($post['username']) ? strip_tags($post['username']) : '';
    $password = isset($post['password']) ? strip_tags($post['password']) : '';
    $email = isset($post['email']) ? strip_tags($post['email']) : '';
    $level = isset($post['level']) ? strip_tags($post['level']) : '';
    $status = isset($post['status']) ? strip_tags($post['status']) : '';
    $no_telp = isset($post['no_telp']) ? strip_tags($post['no_telp']) : '';

    // Insert query with proper column names
    $query = "INSERT INTO user (name, username, password, email, level, status, no_telp)
              VALUES ('$name', '$username', '$password', '$email', '$level', '$status', '$no_telp')";
    
    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}

function register($post) {
    global $conn;
    
    // Validate required fields
    if (!isset($post['name']) || !isset($post['username']) || !isset($post['password'])) {
        return false;
    }

    // Sanitize input
    $name = isset($post['name']) ? strip_tags($post['name']) : '';
    $username = isset($post['username']) ? strip_tags($post['username']) : '';
    $password = isset($post['password']) ? strip_tags($post['password']) : '';
    $email = isset($post['email']) ? strip_tags($post['email']) : '';
    $level = isset($post['level']) ? strip_tags($post['level']) : '';
    $status = isset($post['status']) ? strip_tags($post['status']) : '';
    $no_telp = isset($post['no_telp']) ? strip_tags($post['no_telp']) : '';

    // Insert query with proper column names
    $query = "INSERT INTO user (name, username, password, email, level, status, no_telp)
              VALUES ('$name', '$username', '$password', '$email', 'user', 'aktif', '$no_telp')";
    
    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}


//fungsi untuk menambahkan kategori
function tambah_kategori($post) {
    global $conn;
    $nama = $post['nama'];

    // query hanya memasukkan nama, karena id auto_increment
    $query = "INSERT INTO kategori (nama_kategori) VALUES ('$nama')";
    mysqli_query($conn, $query);

    return mysqli_affected_rows($conn);
}



//fungsi untuk menampilkan akun
function select_akun($query) {
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)){
        $rows[] = $row;
    }
    return $rows;
}

//Fungsi Ubah akun
function ubah_akun($post){
    global $conn;
    $id = $post['id'];
    $name = $post['name'];
    $username = $post['username'];
    $password = $post['password'];
    $level = $post['level'];
    $status = $post['status'];

    //SQL Ubah Akun
    $query = "UPDATE user SET name= '$name',username='$username', password= '$password', level= '$level', status= '$status' WHERE id_user=$id";
    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}

//Fungsi Ubah kategori
function ubah_kategori($post){
    global $conn;
    $id = $post['id'];
    $nama = $post['nama_kategori'];

    //SQL Ubah Kategori
    $query = "UPDATE kategori SET nama_kategori= '$nama' WHERE id_kategori=$id";
    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}

//Fungsi Hapus Akun
function delete_akun($id, $conn) {
    $query = "DELETE FROM user WHERE id_user = '$id'";
    mysqli_query($conn, $query);
}

//Fungsi Hapus Kategori
function delete_kategori($id, $conn) {
    $query = "DELETE FROM kategori WHERE id_kategori = '$id'";
    mysqli_query($conn, $query);
}


function delete_buku($id, $conn) {
    $query = "DELETE FROM buku WHERE id_buku = '$id'";
    mysqli_query($conn, $query);
}



// Handle delete requests
if (isset($_GET['id_user'])) {
    $id = $_GET['id_user'];
    delete_akun($id, $conn);
    header('Location: hapusakun.php');
    exit;
} else if (isset($_GET['id_kategori'])) {
    $id = $_GET['id_kategori'];
    delete_kategori($id, $conn);
    header('Location: hapuskategori.php');
    exit;
} else if (isset($_GET['id_buku'])) {
    $id = $_GET['id_buku'];
    delete_buku($id, $conn);
    header('Location: hapusbuku.php');
    exit;
} 

?>
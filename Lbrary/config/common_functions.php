<?php
function getGreeting() {
    // Set timezone ke WITA
    date_default_timezone_set('Asia/Makassar');
    
    $hour = date('H');
    if ($hour >= 5 && $hour < 11) {
        return 'Selamat Pagi';
    } elseif ($hour >= 11 && $hour < 15) {
        return 'Selamat Siang';
    } elseif ($hour >= 15 && $hour < 19) {
        return 'Selamat Sore';
    } else {
        return 'Selamat Malam';
    }
}

function getCurrentDateTime() {
    // Set timezone ke WITA
    date_default_timezone_set('Asia/Makassar');
    
    // Format tanggal dalam bahasa Indonesia
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    
    $tanggal = date('d');
    $bulan_index = date('n');
    $tahun = date('Y');
    $waktu = date('H:i');
    
    return "$tanggal {$bulan[$bulan_index]} $tahun, $waktu WITA";
}
?> 
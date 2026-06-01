<?php

// format tanggal jadi bahasa Indonesia
// contoh: "31 Mei 2026"
function format_tanggal($tanggal) {
    $bulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
        4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
        10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    $d = date('j', strtotime($tanggal));
    $m = date('n', strtotime($tanggal));
    $y = date('Y', strtotime($tanggal));

    return $d . ' ' . $bulan[$m] . ' ' . $y;
}

// format angka jadi rupiah
// contoh: 150000 -> "Rp 150.000"
function format_rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// potong teks kalau terlalu panjang
// contoh: "Ini teks panjang sekali..." -> "Ini teks pan..."
function potong_teks($teks, $batas = 50) {
    if (strlen($teks) <= $batas) {
        return $teks;
    }
    return substr($teks, 0, $batas) . '...';
}

// redirect ke halaman lain
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

// cek apakah user sudah login
function sudah_login() {
    return !empty($_SESSION['user_id']);
}

// ambil role user yang sedang login
function get_role() {
    return $_SESSION['role'] ?? null;
}

// ambil nama user yang sedang login
function get_nama() {
    return $_SESSION['name'] ?? 'Tamu';
}

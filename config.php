<?php
// File: config.php
session_start();

// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Kosongkan jika XAMPP tidak menggunakan password
define('DB_NAME', 'db_ppdb_smks');

// Membuat koneksi
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi Gagal: " . $conn->connect_error);
}

// Fungsi helper untuk keamanan dan kemudahan
function redirect($url) {
    header("Location: " . $url);
    exit();
}

function check_login() {
    if (!isset($_SESSION['user_id'])) {
        redirect('login.php');
    }
}

function check_role($role) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        // Jika salah role, tendang ke dashboard masing-masing
        $dashboard = ($_SESSION['role'] === 'admin') ? 'admin/index.php' : 'dashboard_siswa.php';
        redirect($dashboard);
    }
}

function escape($string) {
    global $conn;
    return $conn->real_escape_string($string);
}
?>
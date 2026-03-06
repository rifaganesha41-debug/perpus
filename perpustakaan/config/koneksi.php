<?php
// config/koneksi.php

// Ambil konfigurasi dari environment variable (untuk Vercel/Hosting) 
// atau gunakan default (untuk Localhost)
$host = getenv('DB_HOST') ?: "localhost";
$user = getenv('DB_USER') ?: "root";
$pass = getenv('DB_PASS') ?: "";
$db   = getenv('DB_NAME') ?: "perpus_rifa";
$port = getenv('DB_PORT') ?: "3306";

// Koneksi ke database
$conn = mysqli_init();
if ($conn) {
    // Jika menggunakan SSL (biasanya dibutuhkan oleh Aiven/Managed DB)
    $ca_cert = getenv('DB_SSL_CA');
    if ($ca_cert) {
        $ssl_cert_path = '/tmp/ca-cert.crt';
        file_put_contents($ssl_cert_path, $ca_cert);
        mysqli_ssl_set($conn, NULL, NULL, $ssl_cert_path, NULL, NULL);
    }
    
    if (!@mysqli_real_connect($conn, $host, $user, $pass, $db, $port)) {
        die("Koneksi gagal: " . mysqli_connect_error());
    }
} else {
    die("mysqli_init gagal");
}

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Start session if not started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Vercel Session Persistence Fix (Hybrid Cookie-Session)
 * Karena Vercel stateless, kita simpan data esensial di cookie terenkripsi
 */
if (!isset($_SESSION['id_user']) && isset($_COOKIE['app_auth_sync'])) {
    $sync_data = json_decode(base64_decode($_COOKIE['app_auth_sync']), true);
    if ($sync_data && (time() - $sync_data['time'] < 1800)) {
        $_SESSION['id_user'] = $sync_data['id_user'];
        $_SESSION['nama']    = $sync_data['nama'];
        $_SESSION['role']    = $sync_data['role'];
    }
}

if (isset($_SESSION['id_user'])) {
    $sync_data = base64_encode(json_encode([
        'id_user' => $_SESSION['id_user'],
        'nama'    => $_SESSION['nama'],
        'role'    => $_SESSION['role'],
        'time'    => time()
    ]));
    setcookie('app_auth_sync', $sync_data, [
        'expires' => time() + 1800,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

/**
 * Helper function to prevent SQL Injection and XSS
 */
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags(trim($data))));
}

/**
 * Check session and role
 */
function check_login($role = null) {
    if (!isset($_SESSION['id_user'])) {
        header("Location: ../auth/login.php");
        exit();
    }

    if ($role && $_SESSION['role'] !== $role) {
        if ($_SESSION['role'] == 'admin') {
            header("Location: ../admin/dashboard.php");
        } else {
            header("Location: ../user/dashboard.php");
        }
        exit();
    }

    // Session timeout logic (30 minutes)
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
        session_unset();
        session_destroy();
        header("Location: ../auth/login.php?msg=timeout");
        exit();
    }
    $_SESSION['last_activity'] = time();
}
?>

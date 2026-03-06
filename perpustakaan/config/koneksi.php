<?php
// config/koneksi.php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "perpus_rifa";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Start session if not started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
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

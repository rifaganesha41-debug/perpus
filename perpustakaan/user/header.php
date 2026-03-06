<?php
// user/header.php
require_once '../config/koneksi.php';
check_login('user');

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siswa - Perpustakaan Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-light sticky-top py-3">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="dashboard.php">LibreDigital</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active text-primary fw-bold' : ''; ?>" href="dashboard.php">Daftar Buku</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'pinjam.php' ? 'active text-primary fw-bold' : ''; ?>" href="pinjam.php">Peminjaman Saya</a>
                </li>
            </ul>
            <div class="d-flex align-items-center">
                <span class="text-muted me-3">Halo, <?php echo $_SESSION['nama']; ?></span>
                <a href="../auth/logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
            </div>
        </div>
    </div>
</nav>

<div class="container py-4">

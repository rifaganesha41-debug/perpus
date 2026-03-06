<?php
// auth/register.php
require_once '../config/koneksi.php';

if (isset($_SESSION['id_user'])) {
    header("Location: ../index.php");
    exit();
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama     = sanitize($_POST['nama']);
    $username = sanitize($_POST['username']);
    $password = md5($_POST['password']);
    $kelas    = sanitize($_POST['kelas']);
    $no_hp    = sanitize($_POST['no_hp']);
    $alamat   = sanitize($_POST['alamat']);
    $role     = 'user';

    // Check if username exists
    $check = $conn->prepare("SELECT id_user FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $error = "Username sudah terdaftar!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (nama, username, password, role, kelas, no_hp, alamat) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $nama, $username, $password, $role, $kelas, $no_hp, $alamat);
        
        if ($stmt->execute()) {
            $success = "Registrasi berhasil! Silakan <a href='login.php'>Login</a>";
        } else {
            $error = "Terjadi kesalahan saat registrasi.";
        }
        $stmt->close();
    }
    $check->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Perpustakaan Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .card { max-width: 500px; width: 100%; }
    </style>
</head>
<body class="bg-light">
    <div class="auth-wrapper">
        <div class="card p-4">
            <div class="text-center mb-4">
                <h3 class="fw-bold text-primary">LibreDigital</h3>
                <p class="text-muted">Buat akun baru untuk siswa</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show"><?php echo $error; ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show"><?php echo $success; ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" placeholder="Nama lengkap sesuai kartu siswa" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Username login" required autocomplete="off">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Password login" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kelas</label>
                        <input type="text" name="kelas" class="form-control" placeholder="Contoh: XII RPL 1" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">No. HP</label>
                        <input type="text" name="no_hp" class="form-control" placeholder="08..." required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat lengkap"></textarea>
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary">Daftar Sekarang</button>
                </div>
            </form>
            <div class="text-center mt-3">
                <p class="small text-muted">Sudah punya akun? <a href="login.php">Login</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

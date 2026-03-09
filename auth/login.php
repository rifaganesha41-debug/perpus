<?php
// auth/login.php
require_once '../config/koneksi.php';

if (isset($_SESSION['id_user'])) {
    header("Location: ../index.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize($_POST['username']);
    $password = md5($_POST['password']);

    $stmt = $conn->prepare("SELECT id_user, nama, username, role FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['id_user']  = $user['id_user'];
        $_SESSION['nama']     = $user['nama'];
        $_SESSION['role']     = $user['role'];
        $_SESSION['last_activity'] = time();

        header("Location: ../index.php");
        exit();
    } else {
        $error = "Username atau password salah!";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Perpustakaan Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">
    <div class="auth-wrapper">
        <div class="col-md-4">
            <div class="card p-4">
                <div class="text-center mb-4">
                    <h3 class="fw-bold text-primary">Perpustakaan</h3>
                    <p class="text-muted">Silakan login untuk mengakses perpustakaan</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Masukkan username" required autocomplete="off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>
                <div class="text-center mt-3">
                    <p class="small text-muted">Belum punya akun? <a href="register.php">Daftar sekarang</a></p>
                </div>
            </div>
            <div class="text-center mt-3 text-muted small">
                &copy; 2026 Perpustakaan - UKK RPL
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html>

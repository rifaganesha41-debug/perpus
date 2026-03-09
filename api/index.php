<?php
// index.php
require_once 'config/koneksi.php';

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: user/dashboard.php");
    }
} else {
    header("Location: auth/login.php");
}
exit();
?>

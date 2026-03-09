<?php
// user/kembali.php
require_once '../config/koneksi.php';
check_login('user');

$id_user = $_SESSION['id_user'];

if (isset($_GET['id'])) {
    $id_transaksi = (int)$_GET['id'];
    
    // Verify ownership and status
    $check = $conn->query("SELECT id_buku, status FROM transaksi WHERE id_transaksi = $id_transaksi AND id_user = $id_user")->fetch_assoc();
    
    if ($check && $check['status'] == 'dipinjam') {
        $id_buku = $check['id_buku'];
        
        // Update status
        $conn->query("UPDATE transaksi SET status = 'dikembalikan', tanggal_kembali = CURDATE() WHERE id_transaksi = $id_transaksi");
        
        // Update stock
        $conn->query("UPDATE buku SET stok = stok + 1 WHERE id_buku = $id_buku");
        
        header("Location: pinjam.php?msg=success");
        exit();
    }
}

header("Location: pinjam.php");
exit();
?>

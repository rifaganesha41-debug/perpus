<?php
// user/pinjam.php
include 'header.php';

$id_user = $_SESSION['id_user'];

// Handle Return
if (isset($_GET['kembali'])) {
    $id_transaksi = (int)$_GET['kembali'];
    
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

$history = $conn->query("SELECT t.*, b.judul 
                         FROM transaksi t 
                         JOIN buku b ON t.id_buku = b.id_buku 
                         WHERE t.id_user = $id_user 
                         ORDER BY t.id_transaksi DESC");
?>

<div class="mb-4">
    <h2 class="fw-bold">Peminjaman Saya</h2>
    <p class="text-muted">Daftar buku yang sudah kamu pinjam dan statusnya.</p>
</div>

<?php if (isset($_GET['msg'])): ?>
<div class="alert alert-success alert-dismissible fade show">Buku berhasil dikembalikan! <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="card border-0 shadow-sm p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Judul Buku</th>
                    <th>Tanggal Pinjam</th>
                    <th>Tanggal Kembali</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $history->fetch_assoc()): ?>
                <tr>
                    <td><span class="fw-bold"><?php echo $row['judul']; ?></span></td>
                    <td><?php echo date('d M Y', strtotime($row['tanggal_pinjam'])); ?></td>
                    <td><?php echo $row['tanggal_kembali'] ? date('d M Y', strtotime($row['tanggal_kembali'])) : '-'; ?></td>
                    <td>
                        <span class="badge <?php echo $row['status'] == 'dipinjam' ? 'bg-warning' : 'bg-success'; ?>">
                            <?php echo ucfirst($row['status']); ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($row['status'] == 'dipinjam'): ?>
                        <a href="kembali.php?id=<?php echo $row['id_transaksi']; ?>" class="btn btn-sm btn-outline-success" 
                           onclick="return confirm('Kembalikan buku ini?')">
                            <i class="bi bi-arrow-left-circle me-1"></i> Kembalikan
                        </a>
                        <?php else: ?>
                        <span class="text-success small"><i class="bi bi-check-all"></i> Selesai</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if ($history->num_rows == 0): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">Kamu belum meminjam buku apapun.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>

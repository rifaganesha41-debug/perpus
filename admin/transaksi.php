<?php
// admin/transaksi.php
include 'header.php';

// Handle Return
if (isset($_GET['kembali'])) {
    $id_transaksi = sanitize($_GET['kembali']);
    
    // Check if already returned
    $check = $conn->query("SELECT id_buku, status FROM transaksi WHERE id_transaksi = $id_transaksi")->fetch_assoc();
    
    if ($check['status'] == 'dipinjam') {
        $id_buku = $check['id_buku'];
        
        // Update status
        $conn->query("UPDATE transaksi SET status = 'dikembalikan', tanggal_kembali = CURDATE() WHERE id_transaksi = $id_transaksi");
        
        // Update stock
        $conn->query("UPDATE buku SET stok = stok + 1 WHERE id_buku = $id_buku");
        
        header("Location: transaksi.php?msg=returned");
        exit();
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM transaksi WHERE id_transaksi = $id");
    header("Location: transaksi.php?msg=deleted");
    exit();
}

// Search
$query_search = "";
if (isset($_GET['q'])) {
    $q = sanitize($_GET['q']);
    $query_search = " WHERE u.nama LIKE '%$q%' OR b.judul LIKE '%$q%' ";
}

$transaksi_list = $conn->query("SELECT t.*, u.nama as nama_user, b.judul 
                               FROM transaksi t 
                               JOIN users u ON t.id_user = u.id_user 
                               JOIN buku b ON t.id_buku = b.id_buku 
                               $query_search 
                               ORDER BY t.id_transaksi DESC");

// AJAX search
if (isset($_GET['ajax'])) {
    while ($row = $transaksi_list->fetch_assoc()) {
        $status_bg = $row['status'] == 'dipinjam' ? 'bg-warning' : 'bg-success';
        echo "<tr>
                <td>{$row['nama_user']}</td>
                <td>{$row['judul']}</td>
                <td>" . date('d M Y', strtotime($row['tanggal_pinjam'])) . "</td>
                <td>" . ($row['tanggal_kembali'] ? date('d M Y', strtotime($row['tanggal_kembali'])) : '-') . "</td>
                <td><span class='badge $status_bg'>" . ucfirst($row['status']) . "</span></td>
                <td>";
        if ($row['status'] == 'dipinjam') {
            echo "<a href='?kembali={$row['id_transaksi']}' class='btn btn-sm btn-success me-1' onclick='return confirm(\"Konfirmasi pengembalian?\")'>
                    <i class='bi bi-check-circle'></i> Kembali
                  </a>";
        }
        echo "<a href='?delete={$row['id_transaksi']}' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Hapus transaksi ini?\")'>
                <i class='bi bi-trash'></i>
              </a>
              </td>
            </tr>";
    }
    exit();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Laporan Transaksi</h2>
    <div class="d-flex gap-2">
        <input type="text" id="searchTransaksi" class="form-control" placeholder="Cari transaksi..." style="width: 250px;">
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php 
            if ($_GET['msg'] == 'returned') echo "Buku berhasil dikembalikan!"; 
            if ($_GET['msg'] == 'deleted') echo "Transaksi berhasil dihapus!";
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Siswa</th>
                    <th>Judul Buku</th>
                    <th>Pinjam</th>
                    <th>Kembali</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="transaksiTableBody">
                <?php while ($row = $transaksi_list->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['nama_user']; ?></td>
                    <td><?php echo $row['judul']; ?></td>
                    <td><?php echo date('d M Y', strtotime($row['tanggal_pinjam'])); ?></td>
                    <td><?php echo $row['tanggal_kembali'] ? date('d M Y', strtotime($row['tanggal_kembali'])) : '-'; ?></td>
                    <td><span class="badge <?php echo $row['status'] == 'dipinjam' ? 'bg-warning' : 'bg-success'; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                    <td>
                        <?php if ($row['status'] == 'dipinjam'): ?>
                        <a href="?kembali=<?php echo $row['id_transaksi']; ?>" class="btn btn-sm btn-success me-1" onclick="return confirm('Konfirmasi pengembalian?')">
                            <i class="bi bi-check-circle"></i> Kembali
                        </a>
                        <?php endif; ?>
                        <a href="?delete=<?php echo $row['id_transaksi']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus transaksi ini?')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if ($transaksi_list->num_rows == 0): ?>
                <tr><td colspan="6" class="text-center text-muted">Belum ada transaksi</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    liveSearch('searchTransaksi', 'transaksiTableBody', 'transaksi.php?ajax=1');
});
</script>

<?php include 'footer.php'; ?>

<?php
// admin/dashboard.php
include 'header.php';

// Fetch Statistics
$total_buku = $conn->query("SELECT COUNT(*) as total FROM buku")->fetch_assoc()['total'];
$total_anggota = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'")->fetch_assoc()['total'];
$total_transaksi = $conn->query("SELECT COUNT(*) as total FROM transaksi")->fetch_assoc()['total'];
$pinjam_aktif = $conn->query("SELECT COUNT(*) as total FROM transaksi WHERE status = 'dipinjam'")->fetch_assoc()['total'];

// Recent Transactions
$recent_stmt = $conn->query("SELECT t.*, u.nama as nama_user, b.judul 
                             FROM transaksi t 
                             JOIN users u ON t.id_user = u.id_user 
                             JOIN buku b ON t.id_buku = b.id_buku 
                             ORDER BY t.id_transaksi DESC LIMIT 5");
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
    <div class="text-muted">Selamat datang, <?php echo $_SESSION['nama']; ?></div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card h-100 p-3">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                    <i class="bi bi-book text-primary fs-3"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-0">Total Buku</h6>
                    <h3 class="fw-bold mb-0"><?php echo $total_buku; ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card h-100 p-3">
            <div class="d-flex align-items-center">
                <div class="bg-success bg-opacity-10 p-3 rounded-3 me-3">
                    <i class="bi bi-people text-success fs-3"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-0">Total Siswa</h6>
                    <h3 class="fw-bold mb-0"><?php echo $total_anggota; ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card h-100 p-3">
            <div class="d-flex align-items-center">
                <div class="bg-info bg-opacity-10 p-3 rounded-3 me-3">
                    <i class="bi bi-journal-check text-info fs-3"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-0">Total Transaksi</h6>
                    <h3 class="fw-bold mb-0"><?php echo $total_transaksi; ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card h-100 p-3">
            <div class="d-flex align-items-center">
                <div class="bg-warning bg-opacity-10 p-3 rounded-3 me-3">
                    <i class="bi bi-clock-history text-warning fs-3"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-0">Dipinjam</h6>
                    <h3 class="fw-bold mb-0"><?php echo $pinjam_aktif; ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-xl-8">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold mb-4">Transaksi Terbaru</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Siswa</th>
                            <th>Buku</th>
                            <th>Tanggal Pinjam</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $recent_stmt->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['nama_user']; ?></td>
                            <td><?php echo $row['judul']; ?></td>
                            <td><?php echo date('d M Y', strtotime($row['tanggal_pinjam'])); ?></td>
                            <td>
                                <span class="badge <?php echo $row['status'] == 'dipinjam' ? 'bg-warning' : 'bg-success'; ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if ($recent_stmt->num_rows == 0): ?>
                        <tr><td colspan="4" class="text-center text-muted">Belum ada transaksi</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-4">
        <div class="card border-0 shadow-sm p-4">
            <h5 class="fw-bold mb-4">Informasi Sistem</h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                    Versi PHP <span><?php echo PHP_VERSION; ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                    Database <span>MySQL (MariaDB)</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                    Waktu Server <span><?php echo date('H:i'); ?> WIB</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

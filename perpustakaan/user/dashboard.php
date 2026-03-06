<?php
// user/dashboard.php
include 'header.php';

$msg = "";
$error = "";

// Handle Borrow Action
if (isset($_GET['pinjam'])) {
    $id_buku = (int)$_GET['pinjam'];
    $id_user = $_SESSION['id_user'];
    $tanggal = date('Y-m-d');

    // Check stock
    $check = $conn->query("SELECT stok, judul FROM buku WHERE id_buku = $id_buku")->fetch_assoc();
    
    if ($check['stok'] > 0) {
        // Insert transaction
        $stmt = $conn->prepare("INSERT INTO transaksi (id_user, id_buku, tanggal_pinjam, status) VALUES (?, ?, ?, 'dipinjam')");
        $stmt->bind_param("iis", $id_user, $id_buku, $tanggal);
        
        if ($stmt->execute()) {
            // Update stock
            $conn->query("UPDATE buku SET stok = stok - 1 WHERE id_buku = $id_buku");
            $msg = "Berhasil meminjam buku " . $check['judul'];
        } else {
            $error = "Gagal memproses peminjaman.";
        }
    } else {
        $error = "Maaf, stok buku " . $check['judul'] . " sedang kosong!";
    }
}

// Search
$query_search = "";
if (isset($_GET['q'])) {
    $q = sanitize($_GET['q']);
    $query_search = " WHERE judul LIKE '%$q%' OR pengarang LIKE '%$q%' ";
}

$buku_list = $conn->query("SELECT * FROM buku $query_search ORDER BY id_buku DESC");

// AJAX search
if (isset($_GET['ajax'])) {
    while ($row = $buku_list->fetch_assoc()) {
        $disabled = $row['stok'] <= 0 ? 'disabled' : '';
        $stok_class = $row['stok'] > 0 ? 'text-success' : 'text-danger';
        echo "<div class='col-md-4 mb-4'>
                <div class='card h-100 border-0 shadow-sm'>
                    <div class='card-body'>
                        <h5 class='card-title fw-bold'>{$row['judul']}</h5>
                        <p class='text-muted mb-2'>By {$row['pengarang']}</p>
                        <div class='d-flex justify-content-between align-items-center mt-3'>
                            <span class='small $stok_class'>Stok: {$row['stok']}</span>
                            <span class='small text-muted'>{$row['lokasi_rak']}</span>
                        </div>
                        <div class='d-grid mt-3'>
                            <a href='?pinjam={$row['id_buku']}' class='btn btn-primary $disabled'>
                                " . ($row['stok'] > 0 ? 'Pinjam Sekarang' : 'Stok Kosong') . "
                            </a>
                        </div>
                    </div>
                </div>
              </div>";
    }
    exit();
}
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold">Koleksi Buku</h2>
        <p class="text-muted">Temukan buku favoritmu dan mulai membaca hari ini.</p>
    </div>
    <div class="col-md-4">
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
            <input type="text" id="searchBukuUser" class="form-control border-start-0" placeholder="Cari judul atau pengarang...">
        </div>
    </div>
</div>

<?php if ($msg): ?>
<div class="alert alert-success alert-dismissible fade show"><?php echo $msg; ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show"><?php echo $error; ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="row" id="bookContainer">
    <?php while ($row = $buku_list->fetch_assoc()): ?>
    <div class="col-md-4 mb-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="mb-2"><span class="badge bg-secondary opacity-75"><?php echo $row['penerbit']; ?></span></div>
                <h5 class="card-title fw-bold"><?php echo $row['judul']; ?></h5>
                <p class="text-muted mb-2">By <?php echo $row['pengarang']; ?></p>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <span class="small <?php echo $row['stok'] > 0 ? 'text-success' : 'text-danger'; ?>">Stok: <?php echo $row['stok']; ?></span>
                    <span class="small text-muted"><?php echo $row['lokasi_rak']; ?></span>
                </div>
                <div class="d-grid mt-3">
                    <a href="?pinjam=<?php echo $row['id_buku']; ?>" class="btn btn-primary <?php echo $row['stok'] <= 0 ? 'disabled' : ''; ?>" 
                       onclick="return confirm('Pinjam buku ini?')">
                        <?php echo $row['stok'] > 0 ? 'Pinjam Sekarang' : 'Stok Kosong'; ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
    <?php if ($buku_list->num_rows == 0): ?>
    <div class="col-12 text-center py-5">
        <i class="bi bi-book text-muted fs-1"></i>
        <p class="text-muted mt-2">Buku tidak ditemukan.</p>
    </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    liveSearch('searchBukuUser', 'bookContainer', 'dashboard.php?ajax=1');
});
</script>

<?php include 'footer.php'; ?>

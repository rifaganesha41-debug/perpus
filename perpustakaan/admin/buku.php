<?php
// admin/buku.php
include 'header.php';

// Handle Actions (Add, Edit, Delete)
$msg = "";
if (isset($_POST['save_buku'])) {
    $judul      = sanitize($_POST['judul']);
    $pengarang  = sanitize($_POST['pengarang']);
    $penerbit   = sanitize($_POST['penerbit']);
    $tahun      = sanitize($_POST['tahun_terbit']);
    $stok       = sanitize($_POST['stok']);
    $rak        = sanitize($_POST['lokasi_rak']);

    if (isset($_POST['id_buku']) && $_POST['id_buku'] != "") {
        // Edit
        $id = $_POST['id_buku'];
        $stmt = $conn->prepare("UPDATE buku SET judul=?, pengarang=?, penerbit=?, tahun_terbit=?, stok=?, lokasi_rak=? WHERE id_buku=?");
        $stmt->bind_param("ssssisi", $judul, $pengarang, $penerbit, $tahun, $stok, $rak, $id);
    } else {
        // Add
        $stmt = $conn->prepare("INSERT INTO buku (judul, pengarang, penerbit, tahun_terbit, stok, lokasi_rak) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssis", $judul, $pengarang, $penerbit, $tahun, $stok, $rak);
    }

    if ($stmt->execute()) $msg = "success";
    else $msg = "error";
    $stmt->close();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM buku WHERE id_buku = $id");
    header("Location: buku.php?msg=deleted");
    exit();
}

// Search Logic (for AJAX or Normal)
$query_search = "";
if (isset($_GET['q'])) {
    $q = sanitize($_GET['q']);
    $query_search = " WHERE judul LIKE '%$q%' OR pengarang LIKE '%$q%' ";
}

$buku_list = $conn->query("SELECT * FROM buku $query_search ORDER BY id_buku DESC");

// If AJAX search request
if (isset($_GET['ajax'])) {
    while ($row = $buku_list->fetch_assoc()) {
        echo "<tr>
                <td>{$row['judul']}</td>
                <td>{$row['pengarang']}</td>
                <td>{$row['penerbit']} ({$row['tahun_terbit']})</td>
                <td><span class='badge bg-info'>{$row['stok']}</span></td>
                <td>{$row['lokasi_rak']}</td>
                <td>
                    <button class='btn btn-sm btn-outline-primary edit-btn' data-bs-toggle='modal' data-bs-target='#bukuModal' 
                        data-id='{$row['id_buku']}' data-judul='{$row['judul']}' data-pengarang='{$row['pengarang']}'
                        data-penerbit='{$row['penerbit']}' data-tahun='{$row['tahun_terbit']}' data-stok='{$row['stok']}'
                        data-rak='{$row['lokasi_rak']}'>
                        <i class='bi bi-pencil'></i>
                    </button>
                    <a href='?delete={$row['id_buku']}' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Hapus buku ini?\")'>
                        <i class='bi bi-trash'></i>
                    </a>
                </td>
            </tr>";
    }
    exit();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Manajemen Buku</h2>
    <div class="d-flex gap-2">
        <input type="text" id="searchBuku" class="form-control" placeholder="Cari buku..." style="width: 250px;">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bukuModal" id="addBtn">
            <i class="bi bi-plus-lg me-2"></i> Tambah Buku
        </button>
    </div>
</div>

<?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
<div class="alert alert-success alert-dismissible fade show">Buku berhasil dihapus! <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="card border-0 shadow-sm p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Pengarang</th>
                    <th>Penerbit</th>
                    <th>Stok</th>
                    <th>Rak</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="bukuTableBody">
                <?php while ($row = $buku_list->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['judul']; ?></td>
                    <td><?php echo $row['pengarang']; ?></td>
                    <td><?php echo $row['penerbit']; ?> (<?php echo $row['tahun_terbit']; ?>)</td>
                    <td><span class="badge bg-info"><?php echo $row['stok']; ?></span></td>
                    <td><?php echo $row['lokasi_rak']; ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary edit-btn" data-bs-toggle="modal" data-bs-target="#bukuModal" 
                            data-id="<?php echo $row['id_buku']; ?>" data-judul="<?php echo $row['judul']; ?>" 
                            data-pengarang="<?php echo $row['pengarang']; ?>" data-penerbit="<?php echo $row['penerbit']; ?>" 
                            data-tahun="<?php echo $row['tahun_terbit']; ?>" data-stok="<?php echo $row['stok']; ?>" 
                            data-rak="<?php echo $row['lokasi_rak']; ?>">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <a href="?delete=<?php echo $row['id_buku']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus buku ini?')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Buku -->
<div class="modal fade" id="bukuModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Buku</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id_buku" id="id_buku">
                <div class="mb-3">
                    <label class="form-label">Judul</label>
                    <input type="text" name="judul" id="judul" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Pengarang</label>
                    <input type="text" name="pengarang" id="pengarang" class="form-control" required>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label">Penerbit</label>
                        <input type="text" name="penerbit" id="penerbit" class="form-control" required>
                    </div>
                    <div class="col">
                        <label class="form-label">Tahun</label>
                        <input type="number" name="tahun_terbit" id="tahun" class="form-control" required min="1900" max="2100">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label">Stok</label>
                        <input type="number" name="stok" id="stok" class="form-control" required min="0">
                    </div>
                    <div class="col">
                        <label class="form-label">Lokasi Rak</label>
                        <input type="text" name="lokasi_rak" id="rak" class="form-control" placeholder="Contoh: RAK-01">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" name="save_buku" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // AJAX Search
    liveSearch('searchBuku', 'bukuTableBody', 'buku.php?ajax=1');

    // Fill Modal for Edit
    const editBtns = document.querySelectorAll('.edit-btn');
    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('modalTitle').innerText = 'Edit Buku';
            document.getElementById('id_buku').value = this.dataset.id;
            document.getElementById('judul').value = this.dataset.judul;
            document.getElementById('pengarang').value = this.dataset.pengarang;
            document.getElementById('penerbit').value = this.dataset.penerbit;
            document.getElementById('tahun').value = this.dataset.tahun;
            document.getElementById('stok').value = this.dataset.stok;
            document.getElementById('rak').value = this.dataset.rak;
        });
    });

    document.getElementById('addBtn').addEventListener('click', () => {
        document.getElementById('modalTitle').innerText = 'Tambah Buku';
        document.getElementById('id_buku').value = '';
        document.querySelector('#bukuModal form').reset();
    });
});
</script>

<?php include 'footer.php'; ?>

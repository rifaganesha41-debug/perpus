<?php
// admin/anggota.php
include 'header.php';

// Handle Actions (Add, Edit, Delete for User Role)
if (isset($_POST['save_anggota'])) {
    $nama     = sanitize($_POST['nama']);
    $username = sanitize($_POST['username']);
    $kelas    = sanitize($_POST['kelas']);
    $alamat   = sanitize($_POST['alamat']);
    $no_hp    = sanitize($_POST['no_hp']);
    $role     = 'user';

    if (isset($_POST['id_user']) && $_POST['id_user'] != "") {
        // Edit
        $id = $_POST['id_user'];
        if ($_POST['password'] != "") {
            $password = md5($_POST['password']);
            $stmt = $conn->prepare("UPDATE users SET nama=?, username=?, password=?, kelas=?, alamat=?, no_hp=? WHERE id_user=? AND role='user'");
            $stmt->bind_param("ssssssi", $nama, $username, $password, $kelas, $alamat, $no_hp, $id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET nama=?, username=?, kelas=?, alamat=?, no_hp=? WHERE id_user=? AND role='user'");
            $stmt->bind_param("sssssi", $nama, $username, $kelas, $alamat, $no_hp, $id);
        }
    } else {
        // Add
        $password = md5($_POST['password'] != "" ? $_POST['password'] : 'siswa123'); // Default password if empty
        $stmt = $conn->prepare("INSERT INTO users (nama, username, password, role, kelas, alamat, no_hp) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $nama, $username, $password, $role, $kelas, $alamat, $no_hp);
    }
    
    try {
        $stmt->execute();
    } catch (Exception $e) {
        $error_msg = "Gagal menyimpan data: " . $e->getMessage();
    }
    $stmt->close();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM users WHERE id_user = $id AND role = 'user'");
    header("Location: anggota.php");
    exit();
}

// Search Logic
$query_search = " WHERE role = 'user' ";
if (isset($_GET['q'])) {
    $q = sanitize($_GET['q']);
    $query_search .= " AND (nama LIKE '%$q%' OR username LIKE '%$q%' OR kelas LIKE '%$q%') ";
}

$anggota_list = $conn->query("SELECT * FROM users $query_search ORDER BY id_user DESC");

// AJAX search
if (isset($_GET['ajax'])) {
    while ($row = $anggota_list->fetch_assoc()) {
        echo "<tr>
                <td>{$row['nama']}</td>
                <td>{$row['username']}</td>
                <td><span class='badge bg-secondary'>{$row['kelas']}</span></td>
                <td>{$row['no_hp']}</td>
                <td>{$row['alamat']}</td>
                <td>
                    <button class='btn btn-sm btn-outline-primary edit-btn' data-bs-toggle='modal' data-bs-target='#anggotaModal' 
                        data-id='{$row['id_user']}' data-nama='{$row['nama']}' data-username='{$row['username']}'
                        data-kelas='{$row['kelas']}' data-hp='{$row['no_hp']}' data-alamat='{$row['alamat']}'>
                        <i class='bi bi-pencil'></i>
                    </button>
                    <a href='?delete={$row['id_user']}' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Hapus anggota ini?\")'>
                        <i class='bi bi-trash'></i>
                    </a>
                </td>
            </tr>";
    }
    exit();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Data Anggota (Siswa)</h2>
    <div class="d-flex gap-2">
        <input type="text" id="searchAnggota" class="form-control" placeholder="Cari anggota..." style="width: 250px;">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#anggotaModal" id="addBtn">
            <i class="bi bi-person-plus me-2"></i> Tambah Anggota
        </button>
    </div>
</div>

<?php if (isset($error_msg)): ?>
    <div class="alert alert-danger alert-dismissible fade show"><?php echo $error_msg; ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="card border-0 shadow-sm p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Kelas</th>
                    <th>No. HP</th>
                    <th>Alamat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="anggotaTableBody">
                <?php while ($row = $anggota_list->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['nama']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                    <td><span class="badge bg-secondary"><?php echo $row['kelas']; ?></span></td>
                    <td><?php echo $row['no_hp']; ?></td>
                    <td><?php echo $row['alamat']; ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary edit-btn" data-bs-toggle="modal" data-bs-target="#anggotaModal" 
                            data-id="<?php echo $row['id_user']; ?>" data-nama="<?php echo $row['nama']; ?>" 
                            data-username="<?php echo $row['username']; ?>"
                            data-kelas="<?php echo $row['kelas']; ?>" data-hp="<?php echo $row['no_hp']; ?>" 
                            data-alamat="<?php echo $row['alamat']; ?>">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <a href="?delete=<?php echo $row['id_user']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus anggota ini?')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if ($anggota_list->num_rows == 0): ?>
                <tr><td colspan="6" class="text-center text-muted">Belum ada data anggota</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Anggota -->
<div class="modal fade" id="anggotaModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="" method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Anggota</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id_user" id="id_user">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama" id="nama" class="form-control" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" id="username" class="form-control" required autocomplete="off">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kelas</label>
                        <input type="text" name="kelas" id="kelas" class="form-control" placeholder="Contoh: XII RPL 1" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">No. HP</label>
                        <input type="text" name="no_hp" id="no_hp" class="form-control" placeholder="08..." required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" id="alamat" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="submit" name="save_anggota" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // AJAX Search
    liveSearch('searchAnggota', 'anggotaTableBody', 'anggota.php?ajax=1');

    const editBtns = document.querySelectorAll('.edit-btn');
    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('modalTitle').innerText = 'Edit Anggota';
            document.getElementById('id_user').value = this.dataset.id;
            document.getElementById('nama').value = this.dataset.nama;
            document.getElementById('username').value = this.dataset.username;
            document.getElementById('kelas').value = this.dataset.kelas;
            document.getElementById('no_hp').value = this.dataset.hp;
            document.getElementById('alamat').value = this.dataset.alamat;
            document.getElementById('password').placeholder = "Kosongkan jika tidak diubah";
        });
    });

    document.getElementById('addBtn').addEventListener('click', () => {
        document.getElementById('modalTitle').innerText = 'Tambah Anggota';
        document.getElementById('id_user').value = '';
        document.querySelector('#anggotaModal form').reset();
        document.getElementById('password').placeholder = "Jika kosong: siswa123";
    });
});
</script>

<?php include 'footer.php'; ?>

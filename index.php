<?php
require_once 'config/database.php';

$sql  = "SELECT * FROM kategori ORDER BY id_kategori DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kategori - UTS Pemrograman Web 2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .table th { background-color: #343a40; color: #fff; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <span class="navbar-brand">📚 Sistem Manajemen Kategori Buku</span>
        <span class="text-white-50 small">UTS Pemrograman Web 2 — UIN Gusdur</span>
    </div>
</nav>

<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Daftar Kategori Buku</h4>
        <a href="create.php" class="btn btn-primary">➕ Tambah Kategori</a>
    </div>

    <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th width="110">Kode</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th width="110">Status</th>
                        <th width="160">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0):
                        $no = 1;
                        while ($row = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><code><?= htmlspecialchars($row['kode_kategori']) ?></code></td>
                        <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                        <td class="text-muted small">
                            <?= $row['deskripsi'] ? htmlspecialchars($row['deskripsi']) : '<em>—</em>' ?>
                        </td>
                        <td>
                            <?php if ($row['status'] === 'Aktif'): ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="edit.php?id=<?= $row['id_kategori'] ?>"
                               class="btn btn-warning btn-sm">✏️ Edit</a>
                            <button class="btn btn-danger btn-sm"
                                    onclick="confirmDelete(<?= $row['id_kategori'] ?>, '<?= htmlspecialchars($row['nama_kategori'], ENT_QUOTES) ?>')">
                                🗑️ Hapus
                            </button>
                        </td>
                    </tr>
                    <?php
                        endwhile;
                    else:
                    ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            Belum ada data kategori. <a href="create.php">Tambah sekarang</a>.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer text-muted small">
            Total: <?= $result->num_rows ?> kategori
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function confirmDelete(id, nama) {
    if (confirm('Yakin ingin menghapus kategori "' + nama + '"?')) {
        window.location.href = 'delete.php?id=' + id;
    }
}
</script>
</body>
</html>
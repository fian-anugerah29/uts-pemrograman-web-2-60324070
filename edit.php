<?php
require_once 'config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    setFlash('error', '❌ ID kategori tidak valid.');
    header("Location: index.php");
    exit;
}

$cekData = $conn->prepare("SELECT * FROM kategori WHERE id_kategori = ?");
$cekData->bind_param("i", $id);
$cekData->execute();
$resultData = $cekData->get_result();

if ($resultData->num_rows === 0) {
    setFlash('error', '❌ Kategori tidak ditemukan.');
    header("Location: index.php");
    exit;
}

$dataLama = $resultData->fetch_assoc();
$cekData->close();

// Inisialisasi variabel dengan data lama
$kode      = $dataLama['kode_kategori'];
$nama      = $dataLama['nama_kategori'];
$deskripsi = $dataLama['deskripsi'];
$status    = $dataLama['status'];
$errors    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $kode      = trim(htmlspecialchars($_POST['kode_kategori'] ?? '', ENT_QUOTES, 'UTF-8'));
    $nama      = trim(htmlspecialchars($_POST['nama_kategori'] ?? '', ENT_QUOTES, 'UTF-8'));
    $deskripsi = trim(htmlspecialchars($_POST['deskripsi']     ?? '', ENT_QUOTES, 'UTF-8'));
    $status    = trim(htmlspecialchars($_POST['status']        ?? '', ENT_QUOTES, 'UTF-8'));

    // Validasi kode
    if (empty($kode)) {
        $errors['kode'] = 'Kode kategori wajib diisi.';
    } elseif (strlen($kode) < 4 || strlen($kode) > 10) {
        $errors['kode'] = 'Kode kategori harus antara 4–10 karakter.';
    } elseif (!preg_match('/^KAT-/', $kode)) {
        $errors['kode'] = 'Kode kategori harus diawali "KAT-".';
    } else {
        $cekDuplikat = $conn->prepare(
            "SELECT id_kategori FROM kategori WHERE kode_kategori = ? AND id_kategori != ?"
        );
        $cekDuplikat->bind_param("si", $kode, $id);
        $cekDuplikat->execute();
        $cekDuplikat->store_result();
        if ($cekDuplikat->num_rows > 0) {
            $errors['kode'] = 'Kode kategori sudah digunakan oleh data lain.';
        }
        $cekDuplikat->close();
    }

    // Validasi nama
    if (empty($nama)) {
        $errors['nama'] = 'Nama kategori wajib diisi.';
    } elseif (strlen($nama) < 3) {
        $errors['nama'] = 'Nama kategori minimal 3 karakter.';
    } elseif (strlen($nama) > 50) {
        $errors['nama'] = 'Nama kategori maksimal 50 karakter.';
    }

    // Validasi deskripsi
    if (!empty($deskripsi) && strlen($deskripsi) > 200) {
        $errors['deskripsi'] = 'Deskripsi maksimal 200 karakter.';
    }

    // Validasi status
    if (!in_array($status, ['Aktif', 'Nonaktif'])) {
        $errors['status'] = 'Status harus Aktif atau Nonaktif.';
    }

    if (empty($errors)) {
        $update = $conn->prepare(
            "UPDATE kategori SET kode_kategori = ?, nama_kategori = ?, deskripsi = ?, status = ?
             WHERE id_kategori = ?"
        );
        $update->bind_param("ssssi", $kode, $nama, $deskripsi, $status, $id);

        if ($update->execute()) {
            setFlash('success', "✅ Kategori \"$nama\" berhasil diperbarui.");
            header("Location: index.php");
            exit;
        } else {
            $errors['db'] = 'Gagal memperbarui data: ' . $conn->error;
        }
        $update->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kategori - UTS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <a href="index.php" class="navbar-brand">📚 Sistem Manajemen Kategori Buku</a>
    </div>
</nav>

<div class="container">
<div class="row justify-content-center">
<div class="col-md-7">

    <div class="card shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">✏️ Edit Kategori <small class="text-muted">(ID: <?= $id ?>)</small></h5>
        </div>
        <div class="card-body">

            <?php if (isset($errors['db'])): ?>
                <div class="alert alert-danger"><?= $errors['db'] ?></div>
            <?php endif; ?>

            <form method="POST" action="edit.php?id=<?= $id ?>" novalidate>

                <!-- Kode Kategori -->
                <div class="mb-3">
                    <label for="kode_kategori" class="form-label fw-semibold">
                        Kode Kategori <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           class="form-control <?= isset($errors['kode']) ? 'is-invalid' : '' ?>"
                           id="kode_kategori" name="kode_kategori"
                           value="<?= htmlspecialchars($kode) ?>"
                           maxlength="10" required>
                    <?php if (isset($errors['kode'])): ?>
                        <div class="invalid-feedback"><?= $errors['kode'] ?></div>
                    <?php else: ?>
                        <div class="form-text">Format: KAT-XXX, 4–10 karakter, unik.</div>
                    <?php endif; ?>
                </div>

                <!-- Nama Kategori -->
                <div class="mb-3">
                    <label for="nama_kategori" class="form-label fw-semibold">
                        Nama Kategori <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           class="form-control <?= isset($errors['nama']) ? 'is-invalid' : '' ?>"
                           id="nama_kategori" name="nama_kategori"
                           value="<?= htmlspecialchars($nama) ?>"
                           maxlength="50" required>
                    <?php if (isset($errors['nama'])): ?>
                        <div class="invalid-feedback"><?= $errors['nama'] ?></div>
                    <?php else: ?>
                        <div class="form-text">3–50 karakter.</div>
                    <?php endif; ?>
                </div>

                <!-- Deskripsi -->
                <div class="mb-3">
                    <label for="deskripsi" class="form-label fw-semibold">Deskripsi</label>
                    <textarea class="form-control <?= isset($errors['deskripsi']) ? 'is-invalid' : '' ?>"
                              id="deskripsi" name="deskripsi"
                              rows="3" maxlength="200"><?= htmlspecialchars($deskripsi) ?></textarea>
                    <?php if (isset($errors['deskripsi'])): ?>
                        <div class="invalid-feedback"><?= $errors['deskripsi'] ?></div>
                    <?php endif; ?>
                </div>

                <!-- Status -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        Status <span class="text-danger">*</span>
                    </label>
                    <?php if (isset($errors['status'])): ?>
                        <div class="text-danger small mb-1"><?= $errors['status'] ?></div>
                    <?php endif; ?>
                    <div class="d-flex gap-4">
                        <div class="form-check">
                            <input class="form-check-input" type="radio"
                                   name="status" id="statusAktif" value="Aktif"
                                   <?= ($status === 'Aktif') ? 'checked' : '' ?>>
                            <label class="form-check-label text-success fw-semibold" for="statusAktif">Aktif</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio"
                                   name="status" id="statusNonaktif" value="Nonaktif"
                                   <?= ($status === 'Nonaktif') ? 'checked' : '' ?>>
                            <label class="form-check-label text-danger fw-semibold" for="statusNonaktif">Nonaktif</label>
                        </div>
                    </div>
                </div>

                <!-- Tombol -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-warning">💾 Update</button>
                    <a href="index.php" class="btn btn-secondary">← Kembali</a>
                </div>

            </form>
        </div>
    </div>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
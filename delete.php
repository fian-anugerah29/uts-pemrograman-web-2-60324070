<?php
require_once 'config/database.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    setFlash('error', '❌ Parameter ID tidak valid.');
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];

if ($id <= 0) {
    setFlash('error', '❌ ID tidak valid.');
    header("Location: index.php");
    exit;
}

$cekData = $conn->prepare("SELECT id_kategori, nama_kategori FROM kategori WHERE id_kategori = ?");
$cekData->bind_param("i", $id);
$cekData->execute();
$result = $cekData->get_result();

if ($result->num_rows === 0) {
    setFlash('error', '❌ Kategori tidak ditemukan.');
    header("Location: index.php");
    exit;
}

$dataKategori = $result->fetch_assoc();
$namaKategori = $dataKategori['nama_kategori'];
$cekData->close();

$delete = $conn->prepare("DELETE FROM kategori WHERE id_kategori = ?");
$delete->bind_param("i", $id);

if ($delete->execute()) {
    if ($delete->affected_rows > 0) {
        setFlash('success', "✅ Kategori \"$namaKategori\" berhasil dihapus.");
    } else {
        setFlash('error', '⚠️ Tidak ada data yang terhapus.');
    }
} else {
    setFlash('error', '❌ Gagal menghapus data: ' . $conn->error);
}

$delete->close();

header("Location: index.php");
exit;
?>
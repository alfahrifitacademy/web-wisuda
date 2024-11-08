<?php
include '../admin/db_connnection.php';

// Periksa koneksi ke database
if (!$koneksi) {
    echo json_encode(['error' => 'Koneksi database gagal: ' . mysqli_connect_error()]);
    exit;
}

// Periksa apakah parameter id_users tersedia
if (!isset($_GET['id_users'])) {
    echo json_encode(['error' => 'ID pengguna tidak disediakan']);
    exit;
}

$id_users = $_GET['id_users'];

// Ambil data dokumen berdasarkan id_users
$dokumen_query = mysqli_query($koneksi, "
    SELECT id_dok, file_akte, file_ijasa, file_pembayaran, status, tgl_wisuda, waktu
    FROM dokumen
    WHERE create_by = '$id_users'
");

if (!$dokumen_query) {
    echo json_encode(['error' => 'Query gagal: ' . mysqli_error($koneksi)]);
    exit;
}

$dokumen_data = mysqli_fetch_all($dokumen_query, MYSQLI_ASSOC);

// Tentukan header sebagai JSON dan tampilkan hasil
header('Content-Type: application/json');
echo json_encode($dokumen_data);
?>

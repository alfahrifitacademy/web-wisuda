<?php
// Set header untuk memperbolehkan CORS dan mengatur konten sebagai JSON
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Termasuk file koneksi ke database
include_once '../admin/db_connnection.php';  // Pastikan path benar

// Cek apakah koneksi berhasil
if (!isset($koneksi) || !$koneksi) {
    echo json_encode(['error' => 'Koneksi database gagal.']);
    exit();
}

// Inisialisasi array data
$data = [];

// Fungsi untuk menghitung total dari tabel tertentu
function getTotal($koneksi, $table) {
    $query = "SELECT COUNT(*) AS total FROM $table";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total'] ?? 0;
    }
    return 0;
}

// Mendapatkan total data untuk setiap tabel
$data['wisuda'] = getTotal($koneksi, 'dokumen');
$data['mahasiswa'] = getTotal($koneksi, 'users');
$data['fakultas'] = getTotal($koneksi, 'fakultas');
$data['jurusan'] = getTotal($koneksi, 'jurusan');

// Mengembalikan data dalam format JSON
echo json_encode($data);
?>

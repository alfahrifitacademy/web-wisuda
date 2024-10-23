<?php
include '../admin/db_connnection.php'; // Masukkan file koneksi database

// Mendapatkan ID user dari parameter URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Mengecek apakah ID valid dan ada di database
if ($id > 0) {
    // Query untuk menghapus data mahasiswa berdasarkan ID
    $sql = "DELETE FROM users WHERE id_users = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirect ke halaman daftar mahasiswa setelah berhasil dihapus
        header('Location: Location: ../admin/dashboard.php');
        exit();
    } else {
        echo "Terjadi kesalahan saat menghapus data: " . $conn->error;
    }
} else {
    echo "Data tidak ditemukan atau ID tidak valid!";
}

$conn->close();
?>

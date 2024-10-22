<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "nama_database");

$id = $_GET['id'];

// Query untuk menghapus data mahasiswa
$sql = "DELETE FROM mahasiswa WHERE id=$id";

if ($conn->query($sql) === TRUE) {
    echo "Data berhasil dihapus!";
    header("Location: DashboardAdmin.php"); // Redirect ke halaman utama
} else {
    echo "Error deleting record: " . $conn->error;
}

$conn->close();
?>

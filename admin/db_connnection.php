<?php
$servername = "localhost";
$username = "root";
$password = ""; // Sesuaikan jika ada password untuk user root
$dbname = "undangan_wisuda"; // Pastikan nama database sesuai

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>

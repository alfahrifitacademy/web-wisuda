<?php
$host = 'localhost';  // Nama host server
$username = 'root';   // Username database (default untuk XAMPP biasanya 'root')
$password = '';       // Password database (default kosong di XAMPP)
$database = 'undangan_wisuda';  // Ganti dengan nama database Anda

// Membuat koneksi ke database
$koneksi = mysqli_connect($host, $username, $password, $database);

// Cek jika koneksi gagal
if (!$koneksi) {
    die('Koneksi database gagal: ' . mysqli_connect_error());
}
?>

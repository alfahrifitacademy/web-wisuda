<?php
include 'admin/db_connnection.php';

// Tangkap data dari form
$nama = $_POST['nama'];
$npm = $_POST['npm'];
$fakultas = $_POST['fakultas'];
$jurusan = $_POST['jurusan'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// Cek apakah password dan confirm password sama
if ($password !== $confirm_password) {
    echo "Password tidak cocok!";
    exit();
}

// Enkripsi password
$passwordHash = password_hash($password, PASSWORD_BCRYPT);

// Insert data ke tabel users
$sql = "INSERT INTO users (nama, password, npm, fakultas, jurusan) VALUES (?, ?, ?, ?, ?)";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("sssii", $nama, $passwordHash, $npm, $fakultas, $jurusan);

if ($stmt->execute()) {
    // Arahkan ke halaman login setelah registrasi berhasil
    header("Location: login.php");
    exit();
} else {
    echo "Registrasi gagal: " . $koneksi->error;
}
?>

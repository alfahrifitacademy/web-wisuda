<?php
session_start(); // Memulai session
// Koneksi ke database
include 'admin/db_connnection.php';

// Ambil data dari form
$nama = $_POST['nama'];
$nim = $_POST['nim'];
$fakultas_id = $_POST['fakultas'];
$jurusan_id = $_POST['jurusan'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// Cek apakah NIM sudah terdaftar
$queryCheckNIM = "SELECT * FROM users WHERE nim = '$nim'";
$resultCheckNIM = mysqli_query($koneksi, $queryCheckNIM);

if (mysqli_num_rows($resultCheckNIM) > 0) {
    // Jika NIM sudah terdaftar, simpan pesan error dan arahkan kembali
    $_SESSION['error_message'] = 'NIM sudah terdaftar. Silakan gunakan NIM lain.';
    header('Location: register.php');
    exit;
}

// Pastikan password dan konfirmasi password cocok
if ($password !== $confirm_password) {
    $_SESSION['error_message'] = 'Password dan konfirmasi password tidak cocok.';
    header('Location: register.php');
    exit;
}

// Enkripsi password sebelum disimpan
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Simpan data pengguna baru ke database
$query = "INSERT INTO users (nama, nim, fakultas, jurusan, password) 
          VALUES ('$nama', '$nim', '$fakultas_id', '$jurusan_id', '$hashed_password')";
if (mysqli_query($koneksi, $query)) {
    // Jika pendaftaran berhasil, simpan pesan sukses dan arahkan ke login.php
    $_SESSION['success_message'] = 'Pendaftaran berhasil! Silakan login.';
    header('Location: login.php');
    exit;
} else {
    // Jika ada error dalam query, simpan pesan error
    $_SESSION['error_message'] = 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.';
    header('Location: register.php');
    exit;
}
?>

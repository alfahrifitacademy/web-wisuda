<?php
session_start();

// Contoh username dan password admin
$admin_username = "admin";
$admin_password = "admin";

// Ambil data dari form login
$username = $_POST['username'];
$password = $_POST['password'];

// Proses validasi login
if ($username == $admin_username && $password == $admin_password) {
    // Jika login berhasil, set session
    $_SESSION['admin'] = $username;
    header("Location: ../DashboardAdmin.php"); // Redirect ke halaman dashboard
} else {
    // Jika login gagal, tampilkan pesan error
    echo "<script>alert('Username atau password salah!'); window.location.href='login.php';</script>";
}
?>

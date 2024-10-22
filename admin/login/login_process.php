<?php
session_start();

// Contoh email dan password admin yang valid
$admin_email = "admin@example.com";
$admin_password = "admin";

// Ambil data dari form login
$email = $_POST['email'];
$password = $_POST['password'];

// Proses validasi login
if ($email == $admin_email && $password == $admin_password) {
    // Jika login berhasil, set session
    $_SESSION['admin'] = $email;
    header("Location: ../dashboard.php"); // Redirect ke halaman dashboard
    exit();
} else {
    // Jika login gagal, tampilkan pesan error
    echo "<script>alert('Email atau password salah!'); window.location.href='index.php';</script>";
    exit();
}
?>

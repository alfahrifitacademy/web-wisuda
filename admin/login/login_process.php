<?php
session_start();
require '../db_connnection.php'; // Koneksi ke database

// Cek apakah form login telah di-submit
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query untuk mendapatkan admin berdasarkan email
    $query = "SELECT * FROM admin WHERE email='$email'";
    $result = mysqli_query($koneksi, $query);

    // Cek apakah email ditemukan
    if (mysqli_num_rows($result) > 0) {
        $admin = mysqli_fetch_assoc($result);

        // Verifikasi password
        if (password_verify($password, $admin['password'])) {
            // Set session admin
            $_SESSION['admin'] = $admin['id_admin'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_email'] = $admin['email'];

            // Redirect ke halaman dashboard admin
            header("Location: ../dashboard.php");
            exit;
        } else {
            // Password salah
            header("Location: ../login/index.php?error=Password salah!");
            exit;
        }
    } else {
        // Email tidak ditemukan
        header("Location: ../login/index.php?error=Email tidak ditemukan!");
        exit;
    }
} else {
    header("Location: ../login/index.php");
    exit;
}
?>

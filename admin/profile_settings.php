<?php
session_start();
require '../admin/db_connnection.php'; // Mengimpor koneksi database

// Periksa apakah admin sudah login
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Ambil data admin dari session
$admin_id = $_SESSION['admin'];

// Mengambil data admin dari database
$query = "SELECT * FROM admin WHERE id_admin='$admin_id'";
$result = mysqli_query($koneksi, $query);
$admin = mysqli_fetch_assoc($result);

// Ganti Foto Profil
if (isset($_POST['update_photo'])) {
    $targetDir = "assets/img/";
    $fileName = basename($_FILES["profile_photo"]["name"]);
    $targetFilePath = $targetDir . uniqid() . "_" . $fileName;

    // Validasi file gambar
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png'];
    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $targetFilePath)) {
            // Update foto profil di database dengan path relatif
            $relativeFilePath = "web-wisuda2/" . $targetFilePath;
            $query = "UPDATE admin SET photo='$relativeFilePath' WHERE id_admin='$admin_id'";
            mysqli_query($koneksi, $query);
            $_SESSION['success'] = "Foto profil berhasil diperbarui.";
        } else {
            $_SESSION['error'] = "Gagal mengunggah foto.";
        }
    } else {
        $_SESSION['error'] = "Format file tidak valid. Hanya JPG, JPEG, dan PNG yang diizinkan.";
    }
}


// Ganti Nama
if (isset($_POST['update_name'])) {
    $new_name = $_POST['name'];
    $query = "UPDATE admin SET name='$new_name' WHERE id_admin='$admin_id'";
    mysqli_query($koneksi, $query);
    $_SESSION['success'] = "Nama berhasil diperbarui.";
}

// Ganti Email
if (isset($_POST['update_email'])) {
    $new_email = $_POST['email'];
    $query = "UPDATE admin SET email='$new_email' WHERE id_admin='$admin_id'";
    mysqli_query($koneksi, $query);
    $_SESSION['success'] = "Email berhasil diperbarui.";
}

// Ganti Password
if (isset($_POST['update_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Verifikasi password lama
    if (password_verify($current_password, $admin['password'])) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $query = "UPDATE admin SET password='$hashed_password' WHERE id_admin='$admin_id'";
            mysqli_query($koneksi, $query);
            $_SESSION['success'] = "Password berhasil diperbarui.";
        } else {
            $_SESSION['error'] = "Password baru dan konfirmasi tidak cocok.";
        }
    } else {
        $_SESSION['error'] = "Password saat ini salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="../admin/login/css/style.css">
</head>
<body>
    <div class="profile-container">
        <div class="header">
            <a href="dashboard.php" class="back-button">&larr; Kembali</a>
            <h1>Edit Profile</h1>
        </div>

        <div class="profile-content">
            <!-- Card Avatar -->
            <div class="card avatar-card">
                <img src="../assets/img/customer01.png" alt="Profile Avatar" class="avatar-img">
                <form action="profile_settings.php" method="POST" enctype="multipart/form-data" class="avatar-form">
                    <label class="form-label">Avatar</label>
                    <input type="file" name="profile_photo" accept="image/*">
                </form>
            </div>

            <!-- Card Form -->
            <div class="card form-card">
                <form action="profile_settings.php" method="POST">
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="Admin Utama" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="admin@example.com" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="********" required>
                    </div>
                    <button type="submit" name="update_profile" class="submit-button">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>




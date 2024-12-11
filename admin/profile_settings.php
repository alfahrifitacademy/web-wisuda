<?php
session_start();
require '../admin/db_connnection.php'; // Mengimpor koneksi database

// Periksa apakah admin sudah login
if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit;
}

// Ambil data admin dari session
$admin_id = $_SESSION['admin'];

// Mengambil data admin dari database
$query = "SELECT * FROM admin WHERE id_admin='$admin_id'";
$result = mysqli_query($koneksi, $query);
$admin = mysqli_fetch_assoc($result);

// Simpan Semua Perubahan
if (isset($_POST['update_profile'])) {
    $new_name = $_POST['name'];
    $new_email = $_POST['email'];
    $new_password = $_POST['new_password'];

    // Jika password diisi, hash password baru
    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $query = "UPDATE admin SET name='$new_name', email='$new_email', password='$hashed_password' WHERE id_admin='$admin_id'";
    } else {
        // Jika password tidak diisi, hanya update nama dan email
        $query = "UPDATE admin SET name='$new_name', email='$new_email' WHERE id_admin='$admin_id'";
    }

    mysqli_query($koneksi, $query);
    $_SESSION['success'] = "Data berhasil diperbarui.";
    header("Location: profile_settings.php");
    exit;
}

// Ganti Foto Profil
if (isset($_POST['update_photo'])) {
    $targetDir = "../uploads/foto_profile/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true); // Membuat folder jika belum ada
    }

    $fileName = basename($_FILES["profile_photo"]["name"]);
    $targetFilePath = $targetDir . uniqid() . "_" . $fileName;

    // Validasi file gambar
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png'];
    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $targetFilePath)) {
            // Hapus foto lama jika ada
            if ($admin['photo'] && file_exists("../" . $admin['photo'])) {
                unlink("../" . $admin['photo']);
            }

            // Update foto profil di database dengan path relatif
            $relativeFilePath = "uploads/foto_profile/" . basename($targetFilePath);
            $query = "UPDATE admin SET photo='$relativeFilePath' WHERE id_admin='$admin_id'";
            mysqli_query($koneksi, $query);
            $_SESSION['success'] = "Foto profil berhasil diperbarui.";
            header("Location: profile_settings.php");
            exit;
        } else {
            $_SESSION['error'] = "Gagal mengunggah foto.";
        }
    } else {
        $_SESSION['error'] = "Format file tidak valid. Hanya JPG, JPEG, dan PNG yang diizinkan.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="../assets/css/profil.css">
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
                <form action="profile_settings.php" method="POST" enctype="multipart/form-data" class="avatar-form">
                    <div class="avatar-container">
                        <label class="avatar-title">Avatar</label>
                        <img id="avatarPreview" src="<?= $admin['photo'] ? "../" . $admin['photo'] : "../assets/img/default-profile.svg"; ?>" alt="Profile Avatar" class="avatar-img">
                    </div>
                    <input type="file" name="profile_photo" id="profilePhotoInput" accept="image/*" onchange="previewAvatar()">
                    <button type="submit" name="update_photo" class="submit-button">Update Foto</button>
                </form>
            </div>

            <!-- Card Form -->
            <div class="card form-card">
                <form action="profile_settings.php" method="POST">
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($admin['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($admin['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">Password Baru</label>
                        <div class="password-container">
                            <input type="password" id="new_password" name="new_password" placeholder="Password Baru">
                            <span class="toggle-password" onclick="togglePasswordVisibility()">👁️</span>
                        </div>
                    </div>
                    <button type="submit" name="update_profile" class="submit-button">Simpan</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Preview Foto Profil Sebelum Diupload
        function previewAvatar() {
            const fileInput = document.getElementById('profilePhotoInput');
            const previewImage = document.getElementById('avatarPreview');

            const file = fileInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }

        // Toggle Password Visibility
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('new_password');
            const passwordToggle = document.querySelector('.toggle-password');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordToggle.textContent = '🙈'; // Ubah ikon
            } else {
                passwordInput.type = 'password';
                passwordToggle.textContent = '👁️'; // Ubah ikon
            }
        }
    </script>
</body>

</html>
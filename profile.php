<?php
session_start();
require 'admin/db_connnection.php'; // Mengimpor koneksi database

// Periksa apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Ambil data user dari session
$user_id = $_SESSION['user_id'];

// Mengambil data user dari database
$query = "SELECT * FROM users WHERE id_users='$user_id'";
$result = mysqli_query($koneksi, $query);
$user = mysqli_fetch_assoc($result);

// Mengambil data fakultas untuk dropdown
$fakultasQuery = "SELECT * FROM fakultas";
$fakultasResult = mysqli_query($koneksi, $fakultasQuery);

// Ganti Foto Profil
if (isset($_POST['update_photo'])) {
    $targetDir = "../uploads/foto_profile/"; // Direktori penyimpanan
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true); // Buat folder jika belum ada
    }

    $fileName = basename($_FILES["profile_photo"]["name"]);
    $targetFilePath = $targetDir . uniqid() . "_" . $fileName;

    // Validasi file gambar
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png'];
    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $targetFilePath)) {
            // Hapus foto profil lama jika ada
            if ($user['foto_profile'] && file_exists("../" . $user['foto_profile'])) {
                unlink("../" . $user['foto_profile']);
            }

            // Update foto profil di database dengan path relatif
            $relativeFilePath = "uploads/foto_profile/" . basename($targetFilePath);
            $query = "UPDATE users SET foto_profile='$relativeFilePath' WHERE id_users='$user_id'";
            mysqli_query($koneksi, $query);

            $_SESSION['success'] = "Foto profil berhasil diperbarui.";
            header("Location: profile.php");
            exit;
        } else {
            $_SESSION['error'] = "Gagal mengunggah foto.";
        }
    } else {
        $_SESSION['error'] = "Format file tidak valid. Hanya JPG, JPEG, dan PNG yang diizinkan.";
    }
}

// Ganti Nama Lengkap, Fakultas, dan Jurusan
if (isset($_POST['update_profile'])) {
    $new_name = $_POST['name'];
    $fakultas_id = $_POST['fakultas'];
    $jurusan_id = $_POST['jurusan'];

    $query = "UPDATE users SET nama='$new_name', fakultas='$fakultas_id', jurusan='$jurusan_id' WHERE id_users='$user_id'";
    mysqli_query($koneksi, $query);
    $_SESSION['success'] = "Profil berhasil diperbarui.";
    header("Location: profile.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="assets/css/profil.css">
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
                <form action="profile.php" method="POST" enctype="multipart/form-data" class="avatar-form">
                    <div class="avatar-container">
                        <label class="avatar-title">Avatar</label>
                        <img id="avatarPreview" src="<?= $user['foto_profile'] ? "../" . $user['foto_profile'] : "assets/img/default-profile.svg"; ?>" alt="Profile Avatar" class="avatar-img">
                    </div>
                    <input type="file" name="profile_photo" id="profilePhotoInput" accept="image/*" onchange="previewAvatar()">
                    <button type="submit" name="update_photo" class="submit-button">Update Foto</button>
                </form>

            </div>

            <!-- Card Form -->
            <div class="card form-card">
                <form action="profile.php" method="POST">
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['nama']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="fakultas">Fakultas</label>
                        <select id="fakultas" name="fakultas" onchange="updateJurusan()" required>
                            <option value="">-- Pilih Fakultas --</option>
                            <?php while ($fakultas = mysqli_fetch_assoc($fakultasResult)) { ?>
                                <option value="<?= $fakultas['id_fakultas']; ?>" <?= ($user['fakultas'] == $fakultas['id_fakultas']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($fakultas['fakultas']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="jurusan">Jurusan</label>
                        <select id="jurusan" name="jurusan" required>
                            <option value="">-- Pilih Jurusan --</option>
                            <?php
                            $jurusanQuery = "SELECT * FROM jurusan WHERE fakultas_id='" . $user['fakultas'] . "'";
                            $jurusanResult = mysqli_query($koneksi, $jurusanQuery);
                            while ($jurusan = mysqli_fetch_assoc($jurusanResult)) { ?>
                                <option value="<?= $jurusan['id_jurusan']; ?>" <?= ($user['jurusan'] == $jurusan['id_jurusan']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($jurusan['jurusan']); ?>
                                </option>
                            <?php } ?>
                        </select>
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
    </script>
</body>

</html>
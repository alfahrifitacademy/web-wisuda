<?php
session_start();
// Koneksi ke database
require_once('admin/db_connnection.php');

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Ambil ID pengguna yang login
$user_id = $_SESSION['user_id'];

// Ambil data pengguna dari database
$sql = "SELECT users.nama, users.nim, users.foto_profile, 
                fakultas.fakultas, 
                jurusan.jurusan
        FROM users
        LEFT JOIN fakultas ON users.fakultas = fakultas.id_fakultas
        LEFT JOIN jurusan ON users.jurusan = jurusan.id_jurusan
        WHERE users.id_users = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

// Jika data pengguna ditemukan
if (!$userData) {
    echo "Data pengguna tidak ditemukan.";
    exit();
}

$nama = $userData['nama'];
$nim = $userData['nim'];
$fakultas = $userData['fakultas'];
$jurusan = $userData['jurusan'];
$foto_profil = $userData['foto_profile'];
$fakultas_id = $userData['fakultas'];
$jurusan_id = $userData['jurusan'];

// Ambil data fakultas dan jurusan untuk dropdown
$sqlFakultas = "SELECT * FROM fakultas";
$stmtFakultas = $koneksi->prepare($sqlFakultas);
$stmtFakultas->execute();
$resultFakultas = $stmtFakultas->get_result();

$sqlJurusan = "SELECT * FROM jurusan WHERE fakultas_id = ?";
$stmtJurusan = $koneksi->prepare($sqlJurusan);
$stmtJurusan->bind_param("i", $fakultas_id);
$stmtJurusan->execute();
$resultJurusan = $stmtJurusan->get_result();

// Menangani perubahan foto profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto_profil'])) {
    // Proses upload foto profil
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($_FILES["foto_profil"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Cek apakah file gambar
    if (getimagesize($_FILES["foto_profil"]["tmp_name"]) === false) {
        echo "File bukan gambar.";
        $uploadOk = 0;
    }

    // Cek ukuran file
    if ($_FILES["foto_profil"]["size"] > 500000) {
        echo "Maaf, ukuran file terlalu besar.";
        $uploadOk = 0;
    }

    // Cek format file gambar
    if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
        echo "Maaf, hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
        $uploadOk = 0;
    }

    // Jika tidak ada masalah, upload gambar
    if ($uploadOk === 1) {
        if (move_uploaded_file($_FILES["foto_profil"]["tmp_name"], $targetFile)) {
            // Hapus foto lama jika ada
            if ($foto_profil && file_exists($targetDir . $foto_profil)) {
                unlink($targetDir . $foto_profil);
            }

            // Update foto profil di database
            $sqlUpdateFoto = "UPDATE users SET foto_profil = ? WHERE user_id = ?";
            $stmtUpdateFoto = $koneksi->prepare($sqlUpdateFoto);
            $stmtUpdateFoto->bind_param("si", $targetFile, $user_id);
            $stmtUpdateFoto->execute();
            header("Location: profile.php");
            exit();
        } else {
            echo "Terjadi kesalahan saat mengupload gambar.";
        }
    }
}

if (isset($_GET['fakultas_id'])) {
    $fakultas_id = $_GET['fakultas_id'];

    $sql = "SELECT * FROM jurusan WHERE fakultas_id = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("i", $fakultas_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $jurusan = [];
    while ($row = $result->fetch_assoc()) {
        $jurusan[] = $row;
    }

    echo json_encode($jurusan);
}

if (isset($_POST['submit'])) {
    $user_id = $_SESSION['user_id'];
    $fakultas = $_POST['fakultas'];
    $jurusan = $_POST['jurusan'];
    $foto_profile = $_FILES['foto_profile']['name'];

    // Proses upload foto
    if ($foto_profile) {
        $target_dir = "assets/img/";
        $target_file = $target_dir . basename($foto_profile);
        move_uploaded_file($_FILES['foto_profile']['tmp_name'], $target_file);
    } else {
        $foto_profile = null;  // Tetap menggunakan foto lama jika tidak upload baru
    }

    // Update data pengguna
    $sql = "UPDATE users SET fakultas = ?, jurusan = ?, foto_profile = ? WHERE user_id = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("issi", $fakultas, $jurusan, $foto_profile, $user_id);
    $stmt->execute();

    // Redirect ke profile page
    header("Location: profile.php");
    exit();
}

// Menutup koneksi
$stmt->close();
$koneksi->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Dashboard User</title>
    <link rel="stylesheet" href="assets/css/profil.css">
</head>
<body>
    <div class="container">
        <!-- Main Content -->
        <div class="main">
            <div class="topbar">
                <h1>Profile</h1>
            </div>

            <!-- Mode View -->
            <div class="profile-content">
                <div class="profile-header">
                    <div class="profile-photo">
                        <?php if ($foto_profil): ?>
                            <img src="uploads/<?php echo htmlspecialchars($foto_profil); ?>" alt="Foto Profil" width="150">
                        <?php else: ?>
                            <img src="assets/img/default-profile.png" alt="Foto Profil" width="150">
                        <?php endif; ?>
                    </div>
                    <div class="profile-info">
                        <h2><?php echo htmlspecialchars($nama); ?></h2>
                        <p><strong>NIM:</strong> <?php echo htmlspecialchars($nim); ?></p>
                        <p><strong>Fakultas:</strong> <?php echo htmlspecialchars($fakultas); ?></p>
                        <p><strong>Jurusan:</strong> <?php echo htmlspecialchars($jurusan); ?></p>
                    </div>
                </div>

                <!-- Button untuk Edit -->
                <button class="btn-edit" onclick="toggleEditMode()">Edit Profil</button>
            </div>

            <!-- Mode Edit -->
            <div class="profile-edit" id="profileEdit" style="display: none;">
                <form action="profile.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="foto_profil">Foto Profil:</label>
                        <input type="file" name="foto_profil" id="foto_profil">
                        <button type="button" class="btn-modal" onclick="openModal()">Ganti Foto</button>
                    </div>

                    <div class="form-group">
                        <label for="fakultas">Fakultas:</label>
                        <select name="fakultas_id" id="fakultas_id" onchange="updateJurusan()">
                            <?php while ($rowFakultas = $resultFakultas->fetch_assoc()): ?>
                                <option value="<?php echo $rowFakultas['id_fakultas']; ?>" <?php echo ($rowFakultas['id_fakultas'] == $fakultas_id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($rowFakultas['fakultas']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="jurusan">Jurusan:</label>
                        <select name="jurusan_id" id="jurusan_id">
                            <?php while ($rowJurusan = $resultJurusan->fetch_assoc()): ?>
                                <option value="<?php echo $rowJurusan['id_jurusan']; ?>" <?php echo ($rowJurusan['id_jurusan'] == $jurusan_id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($rowJurusan['jurusan']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn-submit">Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal untuk ganti foto -->
    <div class="modal" id="modalFoto">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Ganti Foto Profil</h2>
            <form action="profile.php" method="POST" enctype="multipart/form-data">
                <input type="file" name="foto_profil" required>
                <button type="submit">Upload Foto</button>
            </form>
        </div>
    </div>

    <script>
        function toggleEditMode() {
            var editMode = document.getElementById('profileEdit');
            editMode.style.display = (editMode.style.display === 'none') ? 'block' : 'none';
        }

        function openModal() {
            document.getElementById('modalFoto').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('modalFoto').style.display = 'none';
        }

        function updateJurusan() {
            var fakultasId = document.getElementById('fakultas_id').value;
            var jurusanSelect = document.getElementById('jurusan_id');

            fetch('get_jurusan.php?fakultas_id=' + fakultasId)
                .then(response => response.json())
                .then(data => {
                    jurusanSelect.innerHTML = '';
                    data.forEach(jurusan => {
                        var option = document.createElement('option');
                        option.value = jurusan.id_jurusan;
                        option.textContent = jurusan.jurusan;
                        jurusanSelect.appendChild(option);
                    });
                });
        }
    </script>
</body>
</html>

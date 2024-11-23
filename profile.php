<?php
session_start();
include 'admin/db_connnection.php'; // File koneksi database

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil data user dari database berdasarkan session
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id_users = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Periksa apakah user ditemukan
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    die("Pengguna tidak ditemukan.");
}

// Set default nilai jika data pengguna kosong
$user = [
    'nama' => $user['nama'] ?? '',
    'nim' => isset($user['nim']) ? $user['nim'] : '',
    'fakultas' => isset($user['fakultas']) ? $user['fakultas'] : '',
    'jurusan' => $user['jurusan'] ?? '',
    'foto_profile' => $user['foto_profile'] ?? null,
];

// Ambil daftar fakultas dari tabel fakultas
$fakultas_result = $koneksi->query("SELECT id_fakultas, fakultas FROM fakultas");

// Proses update data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['action'])) {
    $nama = trim($_POST['nama']);
    $fakultas = trim($_POST['fakultas']);
    $jurusan = trim($_POST['jurusan']);

    // Validasi fakultas
    $fakultas_query = "SELECT id_fakultas FROM fakultas WHERE id_fakultas = ?";
    $fakultas_stmt = $koneksi->prepare($fakultas_query);
    $fakultas_stmt->bind_param("i", $fakultas);
    $fakultas_stmt->execute();
    $fakultas_result = $fakultas_stmt->get_result();
    if ($fakultas_result->num_rows === 0) {
        die("Fakultas tidak valid.");
    }

    // Validasi jurusan
    $jurusan_query = "SELECT id_jurusan FROM jurusan WHERE id_jurusan = ?";
    $jurusan_stmt = $koneksi->prepare($jurusan_query);
    $jurusan_stmt->bind_param("i", $jurusan);
    $jurusan_stmt->execute();
    $jurusan_result = $jurusan_stmt->get_result();
    if ($jurusan_result->num_rows === 0) {
        die("Jurusan tidak valid.");
    }

    // Jika ada file foto yang diunggah
    $foto_profile = $user['foto_profile']; // Default foto profil dari database
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['foto']['tmp_name'];
        $foto_profile = file_get_contents($file_tmp); // Baca isi file menjadi binary
    }

    // Update data di database
    $query = "UPDATE users SET nama = ?, fakultas = ?, jurusan = ?, foto_profile = ? WHERE id_users = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("ssssi", $nama, $fakultas, $jurusan, $foto_profile, $user_id);

    if ($stmt->execute()) {
        $success = "Data berhasil diperbarui!";
        // Refresh data user
        $user['nama'] = $nama;
        $user['fakultas'] = $fakultas;
        $user['jurusan'] = $jurusan;
        $user['foto_profile'] = $foto_profile;
    } else {
        $error = "Terjadi kesalahan saat memperbarui data.";
    }
}

// Jika permintaan AJAX untuk mendapatkan jurusan
if (isset($_GET['fakultas_id'])) {
    $fakultas_id = $_GET['fakultas_id'];

    $jurusan_query = "SELECT id_jurusan, jurusan FROM jurusan WHERE fakultas_id = ?";
    $stmt = $koneksi->prepare($jurusan_query);
    $stmt->bind_param("i", $fakultas_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $jurusan = [];
    while ($row = $result->fetch_assoc()) {
        $jurusan[] = $row;
    }

    echo json_encode($jurusan);
    exit();
}

// Jika ada request POST untuk menyimpan foto saja
if (isset($_POST['action']) && $_POST['action'] === 'save_photo') {
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['foto']['tmp_name'];
        $foto_profile = file_get_contents($file_tmp); // Baca isi file menjadi binary

        // Update foto profil di database
        $query = "UPDATE users SET foto_profile = ? WHERE id_users = ?";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("bi", $foto_profile, $user_id);

        if ($stmt->execute()) {
            $success = "Foto profil berhasil diperbarui!";
            $user['foto_profile'] = $foto_profile; // Perbarui foto di variabel user
        } else {
            $error = "Gagal menyimpan foto profil.";
        }
    }
}

// Jika ada request POST untuk menghapus foto
if (isset($_POST['action']) && $_POST['action'] === 'delete_photo') {
    // Set foto profil ke default (null atau file default lainnya)
    $query = "UPDATE users SET foto_profile = NULL WHERE id_users = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        $success = "Foto profil berhasil dihapus!";
        $user['foto_profile'] = null; // Hapus foto dari variabel user
    } else {
        $error = "Gagal menghapus foto profil.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $query = "UPDATE users SET nama = ?, fakultas = ?, jurusan = ?, foto_profile = ? WHERE id_users = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("ssssi", $nama, $fakultas, $jurusan, $foto_profile, $user_id);

    if ($stmt->execute()) {
        $success = "Data berhasil diperbarui!";
    } else {
        $error = "Terjadi kesalahan saat memperbarui data.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Mahasiswa</title>
    <link rel="stylesheet" href="assets/css/profil.css">
</head>

<body>
    <div class="profile-container">
        <!-- Notifikasi -->
        <?php if (isset($success)) : ?>
            <div class="notification success">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error)) : ?>
            <div class="notification error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="profile-card">
            <!-- Nama dan NIM -->
            <h2>Halo, <?php echo htmlspecialchars($user['nama']); ?></h2>
            <p>NIM: <?php echo htmlspecialchars($user['nim']); ?></p>

            <!-- Foto Profil -->
            <div class="profile-photo-wrapper">
                <img id="profile-preview"
                    src="<?php echo (!empty($user['foto_profile']))
                                ? 'data:image/jpeg;base64,' . base64_encode($user['foto_profile'])
                                : 'assets/img/default-profile.svg'; ?>"
                    alt="Foto Profil" class="profile-photo">
                <button class="edit-photo-button" onclick="openModal()">Edit Foto Profil</button>
            </div>

            <!-- Form Data -->
            <form method="POST" enctype="multipart/form-data" class="profile-form">
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" name="nama" id="nama" value="<?php echo htmlspecialchars($user['nama']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="fakultas">Fakultas</label>
                    <select name="fakultas" id="fakultas" required onchange="fetchJurusan()">
                        <option value="">-- Pilih Fakultas --</option>
                        <?php while ($row = $fakultas_result->fetch_assoc()) : ?>
                            <option value="<?php echo $row['id_fakultas']; ?>" <?php echo isset($user['fakultas']) && $row['id_fakultas'] == $user['fakultas'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($row['fakultas']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="jurusan">Jurusan</label>
                    <select name="jurusan" id="jurusan" required>
                        <option value="">-- Pilih Jurusan --</option>
                        <?php
                        if (!empty($user['fakultas'])) {
                            $jurusan_query = "SELECT id_jurusan, jurusan FROM jurusan WHERE fakultas_id = ?";
                            $jurusan_stmt = $koneksi->prepare($jurusan_query);
                            $jurusan_stmt->bind_param("i", $user['fakultas']);
                            $jurusan_stmt->execute();
                            $jurusan_result = $jurusan_stmt->get_result();
                            while ($jurusan_row = $jurusan_result->fetch_assoc()) {
                                echo '<option value="' . $jurusan_row['id_jurusan'] . '"' . ($jurusan_row['id_jurusan'] == $user['jurusan'] ? ' selected' : '') . '>' . htmlspecialchars($jurusan_row['jurusan']) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <button type="submit" class="edit-button">Simpan Perubahan</button>
            </form>
        </div>
    </div>

    <!-- Modal untuk Edit Foto -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeModal()">&times;</span>
            <h2>Edit Foto Profil</h2>
            <img id="modal-profile-preview"
                src="<?php echo (!empty($user['foto_profile']))
                            ? 'data:image/jpeg;base64,' . base64_encode($user['foto_profile'])
                            : 'assets/img/default-profile.svg'; ?>"
                alt="Foto Profil" class="profile-photo">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="save_photo">
                <input type="file" name="foto" id="modal-foto" accept="image/*" onchange="previewModalImage(event)">
                <button type="submit" class="edit-button">Simpan</button>
            </form>

            <form method="POST">
                <input type="hidden" name="action" value="delete_photo">
                <button type="submit" class="delete-button">Hapus Foto</button>
            </form>

        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById("editModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("editModal").style.display = "none";
        }

        function previewModalImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const preview = document.getElementById('modal-profile-preview');
                preview.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        function fetchJurusan() {
            const fakultasId = document.getElementById('fakultas').value;
            const jurusanDropdown = document.getElementById('jurusan');
            jurusanDropdown.innerHTML = '<option value="">-- Pilih Jurusan --</option>';

            if (fakultasId) {
                const xhr = new XMLHttpRequest();
                xhr.open("GET", `profile.php?fakultas_id=${fakultasId}`, true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        const jurusanData = JSON.parse(xhr.responseText);
                        jurusanData.forEach(jurusan => {
                            const option = document.createElement('option');
                            option.value = jurusan.id_jurusan;
                            option.textContent = jurusan.jurusan;
                            jurusanDropdown.appendChild(option);
                        });
                    }
                };
                xhr.send();
            }
        }

        function deleteFoto() {
            document.getElementById('modal-profile-preview').src = 'assets/img/default-profile.svg';
            // Kirim logika penghapusan di server jika perlu
        }

        // Tampilkan notifikasi dengan kelas animasi "show"
        document.querySelectorAll('.notification').forEach(notification => {
            notification.classList.add('show');

            // Hilangkan notifikasi setelah 5 detik
            setTimeout(() => {
                notification.classList.remove('show');
                notification.classList.add('hide');
                // Hapus elemen setelah animasi selesai
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        });
    </script>
</body>

</html>
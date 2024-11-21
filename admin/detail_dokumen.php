<?php
session_start();

// Periksa apakah admin sudah login
if (!isset($_SESSION['admin'])) {
    header("Location: login.php"); // Jika belum login, kembali ke halaman login
    exit;
}

// Hubungkan ke database
include '../admin/db_connnection.php';

// Fungsi untuk update status jika form disubmit
if (isset($_POST['update_status'])) {
    $id_users = $_POST['id_users'];
    $status = $_POST['status'];

    // Update status di tabel dokumen
    $query = "UPDATE dokumen SET status='$status' WHERE create_by='$id_users'";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Status berhasil diperbarui');</script>";
    } else {
        echo "<script>alert('Gagal memperbarui status');</script>";
    }
}

// Ambil id_users dari URL
$id_users = isset($_GET['id_users']) ? $_GET['id_users'] : null;

// Periksa apakah id_users valid
if (!$id_users) {
    echo "ID pengguna tidak valid.";
    exit;
}

// Update Tanggal dan Waktu Wisuda jika form disubmit
if (isset($_POST['update_wisuda'])) {
    $tgl_wisuda = $_POST['tgl_wisuda'];
    $waktu = $_POST['waktu'];

    $update_query = "UPDATE dokumen SET tgl_wisuda = '$tgl_wisuda', waktu = '$waktu' WHERE create_by = '$id_users'";
    if (mysqli_query($koneksi, $update_query)) {
        echo "<script>alert('Tanggal dan Waktu Wisuda berhasil diperbarui');</script>";
    } else {
        echo "<script>alert('Gagal memperbarui Tanggal dan Waktu Wisuda');</script>";
    }
}

// Ambil data pengguna dan dokumen
$user_query = mysqli_query($koneksi, "
    SELECT u.nama, u.nim, f.fakultas, j.jurusan, u.created_at 
    FROM users u
    LEFT JOIN fakultas f ON u.fakultas = f.id_fakultas
    LEFT JOIN jurusan j ON u.jurusan = j.id_jurusan
    WHERE u.id_users = '$id_users'
");
$user = mysqli_fetch_assoc($user_query);

$dokumen_query = mysqli_query($koneksi, "
    SELECT file_akte, file_ijasa, file_pembayaran, status, tgl_wisuda, waktu 
    FROM dokumen 
    WHERE create_by = '$id_users'
");
$dokumen = mysqli_fetch_assoc($dokumen_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Dokumen Mahasiswa</title>
    <!-- ======= Styles ====== -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../admin/assets/css/DashboardAdmin.css">
</head>

<body>
    <div class="container-detail">
        <a href="data_wisuda.php" class="btn-back">← Kembali</a>
        <h2>Detail Dokumen Mahasiswa</h2>

        <!-- Informasi Mahasiswa -->
        <div class="detail-info">
            <div class="column">
                <p><strong>Nama:</strong> <?= $user['nama']; ?></p>
                <p><strong>NIM:</strong> <?= $user['nim']; ?></p>
                <p><strong>Fakultas:</strong> <?= $user['fakultas']; ?></p>
                <p><strong>Jurusan:</strong> <?= $user['jurusan']; ?></p>
            </div>
            <div class="column">
                <p><strong>Status:</strong>
                    <span class="status-badge <?= ($dokumen['status'] == 'Approve') ? 'status-approve' : (($dokumen['status'] == 'Reject') ? 'status-reject' : 'status-pending'); ?>">
                        <?= $dokumen['status'] ?? 'Pending'; ?>
                    </span>
                </p>
            </div>
        </div>

        <!-- Informasi Dokumen -->
        <h3>Dokumen</h3>
        <div class="detail-info">
            <div class="column">
                <p><strong>File Akte:</strong>
                    <?php if (!empty($dokumen['file_akte'])): ?>
                        <a href="../uploads/<?= $dokumen['file_akte'] ?>" target="_blank" class="btn-view">Lihat</a>
                    <?php else: ?>
                        <span class="file-status">Tidak tersedia</span>
                    <?php endif; ?>
                </p>
                <p><strong>File Ijazah:</strong>
                    <?php if (!empty($dokumen['file_ijasa'])): ?>
                        <a href="../uploads/<?= $dokumen['file_ijasa'] ?>" target="_blank" class="btn-view">Lihat</a>
                    <?php else: ?>
                        <span class="file-status">Tidak tersedia</span>
                    <?php endif; ?>
                </p>
                <p><strong>File Pembayaran:</strong>
                    <?php if (!empty($dokumen['file_pembayaran'])): ?>
                        <a href="../uploads/<?= $dokumen['file_pembayaran'] ?>" target="_blank" class="btn-view">Lihat</a>
                    <?php else: ?>
                        <span class="file-status">Tidak tersedia</span>
                    <?php endif; ?>
                </p>
            </div>
            <div class="column">
        <p><strong>Tanggal Wisuda:</strong> 
            <span class="<?= !empty($dokumen['tgl_wisuda']) ? '' : 'file-status'; ?>">
                <?= !empty($dokumen['tgl_wisuda']) ? $dokumen['tgl_wisuda'] : 'Tidak tersedia'; ?>
            </span>
        </p>
        <p><strong>Waktu Wisuda:</strong> 
            <span class="<?= !empty($dokumen['waktu']) ? '' : 'file-status'; ?>">
                <?= !empty($dokumen['waktu']) ? $dokumen['waktu'] : 'Tidak tersedia'; ?>
            </span>
        </p>
    </div>
        </div>


        <!-- Form Edit Tanggal dan Waktu Wisuda -->
        <h3>Edit Tanggal dan Waktu Wisuda</h3>
        <form method="POST">
            <div class="detail-info">
                <div class="column">
                    <label for="tgl_wisuda"><strong>Tanggal Wisuda:</strong></label>
                    <input type="date" id="tgl_wisuda" name="tgl_wisuda" value="<?= $dokumen['tgl_wisuda']; ?>" required>
                </div>
                <div class="column">
                    <label for="waktu"><strong>Waktu Wisuda:</strong></label>
                    <input type="time" id="waktu" name="waktu" value="<?= $dokumen['waktu']; ?>" required>
                </div>
            </div>
            <button type="submit" name="update_wisuda" class="btn-save">Simpan</button>
        </form>

        <!-- Tindakan -->
        <div style="text-align: center; margin-top: 20px;">
            <form method="POST" style="display: inline;">
                <input type="hidden" name="id_users" value="<?= $id_users; ?>">
                <button type="submit" name="status" value="Approve" class="btn-action btn-approve">✔ Terima</button>
                <button type="submit" name="status" value="Reject" class="btn-action btn-reject">✖ Tolak</button>
                <input type="hidden" name="update_status" value="true">
            </form>
        </div>
    </div>

    <!-- Modal Preview File -->
    <div id="filePreviewModal" class="form-modal" style="display: none;">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <iframe id="filePreview" style="width: 100%; height: 500px;" frameborder="0"></iframe>
        </div>
    </div>

    <!-- Overlay -->
    <div id="overlay" onclick="closeModal()" style="display: none;"></div>

    <script>
        function previewFile(filePath) {
            if (!filePath) {
                alert("File tidak tersedia");
                return;
            }

            document.getElementById('filePreview').src = '../path/to/files/' + filePath;
            document.getElementById('filePreviewModal').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('filePreviewModal').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('filePreview').src = "";
        }
    </script>
</body>

</html>
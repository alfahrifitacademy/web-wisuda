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

    // Pastikan status diambil dari POST jika ada
    if (isset($_POST['status']) && in_array($_POST['status'], ['pending', 'approved', 'rejected'])) {
        $status = $_POST['status'];
    }

    // Jika status diubah menjadi 'rejected', ambil alasan penolakan dari form
    if ($status == 'rejected' && isset($_POST['reason_reject']) && !empty($_POST['reason_reject'])) {
        $reason_reject = $_POST['reason_reject']; // Ambil alasan penolakan dari form
    }

    // Siapkan query untuk memperbarui status
    $query = "UPDATE dokumen SET status='$status'";

    // Jika alasan penolakan ada, update juga kolom reason_reject
    if ($reason_reject) {
        $query .= ", reason_reject='$reason_reject'";
    }

    // Update status di tabel dokumen
    $query .= " WHERE create_by='$id_users'";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Status berhasil diperbarui');</script>";
        header("Location: detail_dokumen.php?id_users=$id_users"); // Setelah update, refresh halaman
        exit;
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
    SELECT file_akte, file_ijasa, file_pembayaran, status, tgl_wisuda, waktu, reason_reject 
    FROM dokumen 
    WHERE create_by = '$id_users'
");
$dokumen = mysqli_fetch_assoc($dokumen_query);

// Ambil status dari POST (jika ada) atau dari data dokumen
$status = isset($_POST['status']) ? $_POST['status'] : 'pending'; // Default ke 'pending'

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
    SELECT file_akte, file_ijasa, file_pembayaran, status, tgl_wisuda, waktu, reason_reject 
    FROM dokumen 
    WHERE create_by = '$id_users'
");
$dokumen = mysqli_fetch_assoc($dokumen_query);

// Variabel status dan alasan penolakan
$status = isset($dokumen['status']) ? $dokumen['status'] : 'pending'; // Default status adalah 'pending'
$reason_reject = isset($dokumen['reason_reject']) ? $dokumen['reason_reject'] : null;

// Proses ketika status diubah
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil status dari form
    $status = $_POST['status'];
    $id_users = $_POST['id_users'];

    // Validasi status
    if (!in_array($status, ['pending', 'approved', 'rejected'])) {
        echo "<script>alert('Status tidak valid');</script>";
        exit;
    }

    // Jika status diubah menjadi 'rejected', pastikan ada alasan penolakan
    if ($status == 'rejected') {
        if (isset($_POST['reason_reject']) && !empty($_POST['reason_reject'])) {
            $reason_reject = mysqli_real_escape_string($koneksi, $_POST['reason_reject']); // Escape input alasan
        } else {
            echo "<script>alert('Alasan penolakan harus diisi!');</script>";
            exit;
        }
    } else {
        // Jika status bukan 'rejected', tidak perlu alasan penolakan
        $reason_reject = null;
    }

    // Siapkan query untuk memperbarui status dan alasan penolakan jika ada
    $query = "UPDATE dokumen SET status='$status'";
    if ($status == 'rejected' && !empty($reason_reject)) {
        $query .= ", reason_reject='$reason_reject'"; // Menambahkan alasan penolakan jika ada
    }
    $query .= " WHERE create_by='$id_users'";

    // Eksekusi query
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Status berhasil diperbarui');</script>";
        header("Location: detail_dokumen.php?id_users=$id_users"); // Refresh halaman setelah update
        exit;
    } else {
        echo "<script>alert('Gagal memperbarui status');</script>";
    }
}
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
                <p><strong>Nama:</strong> <?= htmlspecialchars($user['nama']); ?></p>
                <p><strong>NIM:</strong> <?= htmlspecialchars($user['nim']); ?></p>
                <p><strong>Fakultas:</strong> <?= htmlspecialchars($user['fakultas']); ?></p>
                <p><strong>Jurusan:</strong> <?= htmlspecialchars($user['jurusan']); ?></p>
            </div>
            <div class="column">
                <p><strong>Status:</strong>
                    <span class="status-badge <?= ($dokumen['status'] == 'approved') ? 'status-approve' : (($dokumen['status'] == 'rejected') ? 'status-reject' : 'status-pending'); ?>">
                        <?= htmlspecialchars($dokumen['status']) ?? 'pending'; ?>
                    </span>
                </p>
                <?php if ($dokumen['status'] == 'rejected' && !empty($dokumen['reason_reject'])): ?>
                    <p><strong>Alasan Penolakan:</strong> <?= htmlspecialchars($dokumen['reason_reject']); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Informasi Dokumen -->
        <h3>Dokumen</h3>
        <div class="detail-info">
            <div class="column">
                <p><strong>File Akte:</strong>
                    <?php if (!empty($dokumen['file_akte'])): ?>
                        <a href="../uploads/<?= htmlspecialchars($dokumen['file_akte']); ?>" target="_blank" class="btn-view">Lihat</a>
                    <?php else: ?>
                        <span class="file-status">Tidak tersedia</span>
                    <?php endif; ?>
                </p>
                <p><strong>File Ijazah:</strong>
                    <?php if (!empty($dokumen['file_ijasa'])): ?>
                        <a href="../uploads/<?= htmlspecialchars($dokumen['file_ijasa']); ?>" target="_blank" class="btn-view">Lihat</a>
                    <?php else: ?>
                        <span class="file-status">Tidak tersedia</span>
                    <?php endif; ?>
                </p>
                <p><strong>File Pembayaran:</strong>
                    <?php if (!empty($dokumen['file_pembayaran'])): ?>
                        <a href="../uploads/<?= htmlspecialchars($dokumen['file_pembayaran']); ?>" target="_blank" class="btn-view">Lihat</a>
                    <?php else: ?>
                        <span class="file-status">Tidak tersedia</span>
                    <?php endif; ?>
                </p>
            </div>
            <div class="column">
                <p><strong>Tanggal Wisuda:</strong>
                    <span class="<?= !empty($dokumen['tgl_wisuda']) ? '' : 'file-status'; ?>">
                        <?= !empty($dokumen['tgl_wisuda']) ? htmlspecialchars($dokumen['tgl_wisuda']) : 'Tidak tersedia'; ?>
                    </span>
                </p>
                <p><strong>Waktu Wisuda:</strong>
                    <span class="<?= !empty($dokumen['waktu']) ? '' : 'file-status'; ?>">
                        <?= !empty($dokumen['waktu']) ? htmlspecialchars($dokumen['waktu']) : 'Tidak tersedia'; ?>
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
                    <input type="date" id="tgl_wisuda" name="tgl_wisuda" value="<?= htmlspecialchars($dokumen['tgl_wisuda']); ?>" required>
                </div>
                <div class="column">
                    <label for="waktu"><strong>Waktu Wisuda:</strong></label>
                    <input type="time" id="waktu" name="waktu" value="<?= htmlspecialchars($dokumen['waktu']); ?>" required>
                </div>
            </div>
            <button type="submit" name="update_wisuda" class="btn-save">Simpan</button>
        </form>

        <!-- Form untuk Mengubah Status -->
        <form method="POST">
            <input type="hidden" name="id_users" value="<?= $id_users; ?>">

            <!-- Tombol Terima (Approved) -->
            <button type="submit" name="status" value="approved" class="btn-action btn-approve">✔ Terima</button>

            <!-- Tombol Tolak (Rejected) -->
            <button type="button" class="btn-action btn-reject" onclick="openModal()">✖ Tolak</button>
        </form>
    </div>
    
    <!-- Modal untuk alasan penolakan -->
    <div id="reasonModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Masukkan Alasan Penolakan</h2>
            <form method="POST">
                <input type="hidden" name="id_users" value="<?= $id_users; ?>">
                <input type="hidden" name="status" value="rejected">
                <textarea name="reason_reject" id="reason_reject" rows="4" placeholder="Masukkan alasan penolakan" required></textarea><br><br>
                <button type="submit" class="btn-save">Simpan Alasan</button>
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

            document.getElementById('filePreview').src = '../uploads/' + filePath;
            document.getElementById('filePreviewModal').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('filePreviewModal').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('filePreview').src = "";
        }

        // Fungsi untuk membuka modal
        function openModal() {
            document.getElementById("reasonModal").style.display = "block";
        }

        // Fungsi untuk menutup modal
        function closeModal() {
            document.getElementById("reasonModal").style.display = "none";
        }

        // Menutup modal jika pengguna mengklik di luar modal
        window.onclick = function(event) {
            var modal = document.getElementById("reasonModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>

</html>
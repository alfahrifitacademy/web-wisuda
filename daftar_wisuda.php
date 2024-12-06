<?php
session_start();
require 'admin/db_connnection.php'; // Pastikan koneksi ke database sudah ada

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Ambil ID pengguna yang sedang login
$userId = $_SESSION['user_id'];

// Query untuk mengambil status dokumen dan alasan penolakan dari database berdasarkan create_by
$sql = "SELECT status, reason_reject FROM dokumen WHERE create_by = ?";  // Gantilah user_id menjadi create_by
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->store_result();

// Cek apakah ada data dokumen untuk pengguna
if ($stmt->num_rows > 0) {
    // Jika ada, ambil status dan alasan penolakan
    $stmt->bind_result($statusDokumen, $reasonReject);
    $stmt->fetch();
} else {
    // Jika tidak ada data dokumen, set status default menjadi 'not_uploaded'
    $statusDokumen = 'not_uploaded'; 
    $reasonReject = ''; // Tidak ada alasan jika tidak ada penolakan
}

$stmt->close();

// Menampilkan pesan sukses jika ada
if (isset($_SESSION['upload_success'])) {
    $message = $_SESSION['upload_success'];
    unset($_SESSION['upload_success']); // Hapus pesan setelah ditampilkan
}

// Cek jika sudah mendaftar wisuda
if (isset($_SESSION['daftar_wisuda']) && $_SESSION['daftar_wisuda'] === true) {
    // Redirect ke dashboard jika sudah mendaftar
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Wisuda</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>

<body>
    <!-- =============== Navigation ================ -->
    <div class="container">
        <!-- ========================= Main ==================== -->
        <div class="main">
            <!-- Form Daftar Wisuda -->
            <div class="content">
                <?php if ($statusDokumen == 'approved'): ?>
                    <!-- Jika dokumen disetujui -->
                    <h2>Dokumen Anda telah disetujui. Anda bisa melanjutkan ke halaman <a href="kartu_undangan.php">Kartu Undangan</a></h2>
                <?php elseif ($statusDokumen == 'rejected'): ?>
                    <!-- Jika dokumen ditolak -->
                    <h2>Dokumen Anda ditolak. Silakan cek alasan penolakan di bawah ini.</h2>
                    <p><strong>Alasan Penolakan:</strong> <?php echo htmlspecialchars($reasonReject); ?></p>
                    
                    <!-- Form upload dokumen jika ditolak -->
                    <h3>Silakan upload ulang dokumen yang benar.</h3>

                    <h2>Form Upload Dokumen Pendaftaran Wisuda</h2>
                    <form action="upload_dokumen.php" method="post" enctype="multipart/form-data">
                        <label for="file_akte">Upload Fotokopi Akte Kelahiran (PDF):</label><br>
                        <input type="file" name="file_akte" id="file_akte" accept="application/pdf" required><br><br>

                        <label for="file_ijasa">Upload Fotokopi Ijazah Terakhir (PDF):</label><br>
                        <input type="file" name="file_ijasa" id="file_ijasa" accept="application/pdf" required><br><br>

                        <label for="file_pembayaran">Upload Bukti Pembayaran Wisuda (PNG/JPG):</label><br>
                        <input type="file" name="file_pembayaran" id="file_pembayaran" accept="image/png, image/jpeg" required><br><br>

                        <button type="submit" name="submit">Upload Dokumen</button>
                    </form>
                <?php elseif ($statusDokumen == 'pending'): ?>
                    <!-- Jika dokumen masih pending -->
                    <h2>Dokumen Anda sedang diproses. Mohon tunggu.</h2>
                    <p>Status dokumen Anda sedang dalam proses verifikasi. Harap bersabar menunggu konfirmasi lebih lanjut.</p>

                    <!-- Tombol Kembali ke Dashboard -->
                    <form action="dashboard.php" method="get">
                        <button type="submit">Kembali ke Dashboard</button>
                    </form>
                <?php elseif ($statusDokumen == 'not_uploaded'): ?>
                    <!-- Jika dokumen belum pernah diupload -->
                    <h2>Syarat Pendaftaran Wisuda</h2>
                    <ul>
                        <li>Mengunggah fotokopi akte kelahiran dalam format PDF.</li>
                        <li>Mengunggah fotokopi ijazah terakhir dalam format PDF.</li>
                        <li>Mengunggah bukti pembayaran biaya wisuda dalam format PNG atau JPG.</li>
                    </ul>

                    <h2>Form Upload Dokumen Pendaftaran Wisuda</h2>
                    <form action="upload_dokumen.php" method="post" enctype="multipart/form-data">
                        <label for="file_akte">Upload Fotokopi Akte Kelahiran (PDF):</label><br>
                        <input type="file" name="file_akte" id="file_akte" accept="application/pdf" required><br><br>

                        <label for="file_ijasa">Upload Fotokopi Ijazah Terakhir (PDF):</label><br>
                        <input type="file" name="file_ijasa" id="file_ijasa" accept="application/pdf" required><br><br>

                        <label for="file_pembayaran">Upload Bukti Pembayaran Wisuda (PNG/JPG):</label><br>
                        <input type="file" name="file_pembayaran" id="file_pembayaran" accept="image/png, image/jpeg" required><br><br>

                        <button type="submit" name="submit">Upload Dokumen</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- =========== Scripts =========  -->
    <script src="assets/js/dashboard.js"></script>
    <script src="assets/js/common.js"></script>

    <!-- ====== ionicons ======= -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>

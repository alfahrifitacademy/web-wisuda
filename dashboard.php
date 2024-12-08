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

// Ambil nama user dari session
$nama_user = isset($_SESSION['nama']) ? $_SESSION['nama'] : "User";

// Ambil status dokumen berdasarkan user_id
$sql = "SELECT status, reason_reject FROM dokumen WHERE create_by = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $user_id);  // Mengikat parameter ID pengguna
$stmt->execute();
$result = $stmt->get_result();
$documentStatus = $result->fetch_assoc(); // Ambil status dokumen

// Periksa apakah hasilnya null
if ($documentStatus) {
    $status = $documentStatus['status'];
    $reason_reject = $documentStatus['reason_reject'];
} else {
    $status = null;
    $reason_reject = null;
    // Opsional: Berikan pesan atau nilai default
    $notificationMessage = "Tidak ada status dokumen untuk pengguna ini.";
}

// Menyimpan status dalam session untuk ditampilkan di frontend
$_SESSION['document_status'] = $documentStatus ?? null;
$_SESSION['notification_read'] = false; // Set notifikasi belum dibaca jika ada perubahan status


if ($status == 'approved') {
    $notificationMessage = 'Dokumen Anda telah disetujui! Anda dapat mengakses Kartu Undangan.';
} elseif ($status == 'rejected') {
    $notificationMessage = 'Dokumen Anda ditolak. Harap daftar ulang. Alasan: ' . htmlspecialchars($reason_reject);
}

// Menyimpan status dalam session untuk ditampilkan di frontend
if ($documentStatus) {
    $_SESSION['document_status'] = $documentStatus;
    $_SESSION['notification_read'] = false; // Set notifikasi belum dibaca
} else {
    $_SESSION['document_status'] = null;
    $_SESSION['notification_read'] = true; // Tidak ada notifikasi
}

// Periksa apakah hasilnya null
if ($documentStatus) {
    $status = $documentStatus['status'];
    $reason_reject = $documentStatus['reason_reject'];
} else {
    $status = null;
    $reason_reject = null;
    // Opsional: Berikan pesan atau nilai default
    $notificationMessage = "Tidak ada status dokumen untuk pengguna ini.";
}

// Menyimpan status dalam session untuk ditampilkan di frontend
$_SESSION['document_status'] = $documentStatus ?? null;
$_SESSION['notification_read'] = false; // Set notifikasi belum dibaca jika ada perubahan status

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
    <title>Dashboard User</title>
    <!-- ======= Styles ====== -->
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>

<body>
    <!-- =============== Navigation ================ -->
    <div class="container">
        <div class="navigation">
            <ul>
                <li>
                    <a href="dashboard.php">
                        <span class="icon">
                            <img src="assets/img/logo.png" alt="Logo">
                        </span>
                        <span class="title">STMIK Bandung</span>
                    </a>
                </li>

                <li>
                    <a href="dashboard.php">
                        <span class="icon">
                            <ion-icon name="home-outline"></ion-icon>
                        </span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="kartu_undangan.php">
                        <span class="icon">
                            <ion-icon name="chatbubble-outline"></ion-icon>
                        </span>
                        <span class="title">Kartu Undangan</span>
                    </a>
                </li>
                <li>
                    <a href="logout.php">
                        <span class="icon">
                            <ion-icon name="log-out-outline"></ion-icon>
                        </span>
                        <span class="title">Sign Out</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- ========================= Main ==================== -->
        <div class="main">
            <!-- =================== Topbar =================== -->
            <div class="topbar">
                <div class="toggle">
                    <ion-icon name="menu-outline"></ion-icon>
                </div>

                <!-- Notifikasi -->
                <div class="notification" onclick="toggleNotification()">
                    <ion-icon name="notifications-outline" id="notificationIcon"></ion-icon>
                    <div class="notification-popup" id="notificationPopup">
                        <div class="popup-content" id="popupContent"></div>
                    </div>
                </div>


                <!-- Foto Profil -->
                <div class="user">
                    <img src="assets/img/customer01.png" alt="pp" />
                </div>
            </div>

            <!-- ============ Main Dashboard Content =========== -->
            <div class="dashboard-content">
                <div class="welcome-section">
                    <h1>Selamat Datang, <span class="highlight"><?php echo htmlspecialchars($nama_user); ?>!</span></h1>
                    <p>Ini adalah dashboard utama Anda, di mana Anda dapat mengakses semua fitur yang tersedia.</p>
                </div>

                <div class="features-section">
                    <div class="feature-box">
                        <h2>Daftar Wisuda</h2>
                        <p>Cek status pendaftaran dan kelola informasi wisuda Anda.</p>
                        <a href="daftar_wisuda.php" class="feature-link">Lihat Detail</a>
                    </div>
                    <div class="feature-box">
                        <h2>Kartu Undangan</h2>
                        <p>Unduh kartu undangan resmi untuk acara wisuda Anda.</p>
                        <a href="kartu_undangan.php" class="feature-link">Lihat Detail</a>
                    </div>
                    <div class="feature-box">
                        <h2>Profil</h2>
                        <p>Perbarui informasi profil Anda dengan mudah.</p>
                        <a href="profile.php" class="feature-link">Lihat Profil</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        window.onload = function() {
            const notificationIcon = document.getElementById('notificationIcon');
            const notificationPopup = document.getElementById('notificationPopup');
            const popupContent = document.getElementById('popupContent');

            // Mendapatkan status dokumen dari session (dari PHP)
            <?php if (isset($_SESSION['document_status'])): ?>
                const status = '<?php echo $_SESSION['document_status']['status']; ?>';
                const reason = '<?php echo $_SESSION['document_status']['reason_reject']; ?>';
                const notificationRead = <?php echo $_SESSION['notification_read'] ? 'true' : 'false'; ?>;

                // Jika notifikasi belum dibaca
                if (!notificationRead) {
                    notificationIcon.style.color = 'red'; // Warna merah untuk notifikasi belum dibaca
                } else {
                    notificationIcon.style.color = 'black'; // Warna hitam jika sudah dibaca
                }

                // Menampilkan isi popup berdasarkan status dokumen
                if (status === 'approved') {
                    popupContent.innerHTML = "<p>Dokumen Anda sudah disetujui! Anda sekarang dapat mengakses <a href='kartu_undangan.php'>Kartu Undangan</a>.</p>";
                } else if (status === 'rejected') {
                    popupContent.innerHTML = `<p>Dokumen Anda ditolak. Alasan: ${reason}. Silakan perbaiki dan <a href='daftar_wisuda.php'>Daftar Ulang</a>.</p>`;
                }
            <?php endif; ?>
        };

        // Fungsi untuk menampilkan notifikasi
        function toggleNotification() {
            const notificationIcon = document.getElementById('notificationIcon');
            const notificationPopup = document.getElementById('notificationPopup');

            // Toggle popout visibility
            notificationPopup.style.display = notificationPopup.style.display === 'block' ? 'none' : 'block';

            // Jika belum dibaca, ubah ikon menjadi hitam
            notificationIcon.style.color = 'black'; // Ikon menjadi hitam saat diklik

            // Update status notifikasi sudah dibaca
            <?php $_SESSION['notification_read'] = true; ?>
        }

        // Menutup popup jika klik di luar elemen notifikasi
        window.addEventListener('click', function(event) {
            const notificationPopup = document.getElementById('notificationPopup');
            const notificationIcon = document.getElementById('notificationIcon');
            if (!notificationIcon.contains(event.target) && !notificationPopup.contains(event.target)) {
                notificationPopup.style.display = 'none';
            }
        });
    </script>


    <!-- =========== Scripts =========  -->
    <script src="assets/js/dashboard.js"></script>
    <script src="assets/js/common.js"></script>

    <!-- ====== ionicons ======= -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>
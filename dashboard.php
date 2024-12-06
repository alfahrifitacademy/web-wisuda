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

// Hitung jumlah notifikasi yang belum dibaca
$notifications = [
    'approved' => 0,
    'rejected' => 0,
    'pending' => 0
];

while ($row = $result->fetch_assoc()) {
    $status = $row['status'];
    if (array_key_exists($status, $notifications)) {
        $notifications[$status]++;
    }
}

// Hitung total notifikasi
$total_notifications = $notifications['approved'] + $notifications['rejected'] + $notifications['pending'];

// Simpan alasan penolakan jika ada
$reason_reject = '';
if ($notifications['rejected'] > 0) {
    $reason_reject = $row['reason_reject'];  // Ambil alasan penolakan terakhir
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

                <!-- Notifikasi dan Foto Profil di kanan -->
                <div class="notification-container">
                    <!-- Ikon lonceng dengan jumlah notifikasi -->
                    <div class="notification-bell">
                        <ion-icon name="notifications-outline"></ion-icon>
                        <div class="notification-count"><?php echo $total_notifications; ?></div>
                    </div>

                    <!-- Popout Notifikasi -->
                    <div class="notification-popup">
                        <h3>Notifikasi</h3>
                        <?php if ($total_notifications > 0): ?>
                            <ul>
                                <?php if ($notifications['approved'] > 0): ?>
                                    <li>Dokumen Anda telah disetujui.</li>
                                <?php endif; ?>

                                <?php if ($notifications['rejected'] > 0): ?>
                                    <li>Dokumen Anda ditolak. Alasan: <?php echo htmlspecialchars($reason_reject); ?></li>
                                <?php endif; ?>

                                <?php if ($notifications['pending'] > 0): ?>
                                    <li>Dokumen Anda sedang diproses. Mohon tunggu.</li>
                                <?php endif; ?>
                            </ul>
                        <?php else: ?>
                            <p>Tidak ada notifikasi baru.</p>
                        <?php endif; ?>
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

    <!-- Script untuk mengatur notifikasi -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ambil elemen lonceng dan popout
            var bellIcon = document.querySelector('.notification-bell');
            var notificationPopup = document.querySelector('.notification-popup');

            // Cek jumlah notifikasi dari PHP
            var notificationsCount = <?php echo $total_notifications; ?>;

            // Jika ada notifikasi, tampilkan tanda merah
            if (notificationsCount > 0) {
                bellIcon.classList.add('has-notifications');
            } else {
                bellIcon.classList.remove('has-notifications');
            }

            // Fungsi untuk toggle popout notifikasi
            function toggleNotificationPopup() {
                notificationPopup.classList.toggle('active');
            }

            // Event listener untuk lonceng
            bellIcon.addEventListener('click', toggleNotificationPopup);
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
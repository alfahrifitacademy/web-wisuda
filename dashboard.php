<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
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
                    <a href="daftar_wisuda.php">
                        <span class="icon">
                            <ion-icon name="people-outline"></ion-icon>
                        </span>
                        <span class="title">Daftar Wisuda</span>
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
            <div class="topbar">
                <div class="toggle">
                    <ion-icon name="menu-outline"></ion-icon>
                </div>
                <div class="user">
                    <img src="assets/img/customer01.png" alt="pp">
                </div>
            </div>

            <!-- ============ Main Dashboard Content =========== -->
            <div class="dashboard-content">
                <div class="welcome-section">
                    <h1>Selamat Datang, <span class="highlight">User!</span></h1>
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

    <!-- =========== Scripts =========  -->
    <script src="assets/js/dashboard.js"></script>
    <script src="assets/js/common.js"></script>

    <!-- ====== ionicons ======= -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>
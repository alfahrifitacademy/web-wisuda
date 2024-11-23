<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Cek jika ada pesan sukses di session
if (isset($_SESSION['upload_success'])) {
    $message = $_SESSION['upload_success'];
    echo "<script>alert('$message');</script>";
    unset($_SESSION['upload_success']); // Hapus pesan setelah ditampilkan
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Wisuda</title>
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
                    <img href="profile.php" src="assets/img/customer01.png" alt="pp">
                </div>
            </div>

           <!-- Form Daftar Wisuda -->
           <div class="content">
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
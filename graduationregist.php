<?php
session_start();

// Cek apakah pengguna sudah login, jika tidak arahkan ke halaman login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
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
    <!-- ======= Styles ====== -->
    <link rel="stylesheet" href="css/graduationregist.css">
</head>

<body>
    <!-- =============== Navigation ================ -->
    <div class="container">
        <div class="navigation">
            <ul>
                <li>
                    <a href="dashboard.php">
                        <span class="icon">
                            <img src="img/logo.png" alt="Logo STMIK Bandung">
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
                    <a href="graduationregist.php">
                        <span class="icon">
                            <ion-icon name="people-outline"></ion-icon>
                        </span>
                        <span class="title">Daftar Wisuda</span>
                    </a>
                </li>

                <li>
                    <a href="invitecard.php">
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
                    <img src="img/customer01.png" alt="pp">
                </div>
            </div>
            <div class="container2">
                <h1>DAFTAR WISUDA ONLINE</h1>
                <form action="process_wisuda.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nama">Nama *</label>
                        <input type="text" name="nama" id="nama" placeholder="Contoh : Gibran Alfi Ananta" required>
                    </div>

                    <div class="form-group">
                        <label for="nim">NIM *</label>
                        <input type="text" name="nim" id="nim" placeholder="Contoh : 211210032" required>
                    </div>

                    <div class="form-group">
                        <label for="jurusan">Jurusan *</label>
                        <input type="text" name="jurusan" id="jurusan" placeholder="Contoh : Sistem Informasi" required>
                    </div>

                    <div class="form-group">
                        <label for="fakultas">Fakultas *</label>
                        <input type="text" name="fakultas" id="fakultas" placeholder="Contoh : Teknologi Informasi" required>
                    </div>

                    <div class="form-group">
                        <label for="akte_kelahiran">Akte Kelahiran *</label>
                        <input type="file" name="akte_kelahiran" id="akte_kelahiran" accept=".jpg,.jpeg,.png,.pdf" required>
                        <small>Max 5MB</small>
                    </div>

                    <div class="form-group">
                        <label for="ijazah">Ijazah *</label>
                        <input type="file" name="ijazah" id="ijazah" accept=".jpg,.jpeg,.png,.pdf" required>
                        <small>Max 5MB</small>
                    </div>

                    <div class="form-group">
                        <label for="bukti_pembayaran">Bukti Pembayaran *</label>
                        <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" accept=".jpg,.jpeg,.png,.pdf" required>
                        <small>Max 5MB</small>
                    </div>

                    <button type="submit" class="submit-btn">SUBMIT</button>
                </form>
            </div>
        </div>
    </div>

    <!-- =========== Scripts =========  -->
    <script src="js/dashboard.js"></script>

    <!-- ====== ionicons ======= -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>
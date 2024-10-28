<?php
session_start();

// Periksa apakah admin sudah login
if (!isset($_SESSION['admin'])) {
    header("Location: login.php"); // Jika belum login, kembali ke halaman login
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- ======= Styles ====== -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../admin/assets/css/DashboardAdmin.css">
</head>

<body>
    <!-- =============== Navigation ================ -->
    <div class="container">
        <div class="navigation">
            <ul>
                <li>
                    <a href="DashboardAdmin.php">
                        <span class="icon">
                            <img src="../admin/assets/imgs/logo.png" alt="Logo">
                        </span>
                        <span class="title">STMIK Bandung</span>
                    </a>
                </li>

                <li>
                    <a href="../admin/dashboard.php">
                        <span class="icon">
                            <ion-icon name="home-outline"></ion-icon>
                        </span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="../admin/data_wisuda.php">
                        <span class="icon">
                            <ion-icon name="people-outline"></ion-icon>
                        </span>
                        <span class="title">Data Wisuda</span>
                    </a>
                </li>

                <li>
                    <a href="../admin/pengunguman.php">
                        <span class="icon">
                            <ion-icon name="chatbubble-outline"></ion-icon>
                        </span>
                        <span class="title">Pengunguman</span>
                    </a>
                </li>

                <li>
                    <a href="../admin/data_mahasiswa.php">
                        <span class="icon">
                            <ion-icon name="people-outline"></ion-icon>
                        </span>
                        <span class="title">Data Mahasiswa</span>
                    </a>
                </li>

                <li>
                    <a href="../admin/data_fakultas.php">
                        <span class="icon">
                            <ion-icon name="people-outline"></ion-icon>
                        </span>
                        <span class="title">Data Fakultas</span>
                    </a>
                </li>

                <li>
                    <a href="../admin/data_jurusan.php">
                        <span class="icon">
                            <ion-icon name="people-outline"></ion-icon>
                        </span>
                        <span class="title">Data Jurusan</span>
                    </a>
                </li>

                <li>
                    <a href="../admin/logout.php">
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
                    <img src="assets/imgs/customer01.jpg" alt="pp">
                </div>
            </div>

            <!-- ======================= Cards ================== -->
            <div class="cardBox">
                <div class="card" id="card-wisuda">
                    <div>
                        <div class="numbers" id="wisuda"></div>
                        <div class="cardName">Data Wisuda</div>
                    </div>
                    <div class="iconBx">
                        <ion-icon name="people-outline"></ion-icon>
                    </div>
                </div>

                <div class="card" id="card-mahasiswa">
                    <div>
                        <div class="numbers" id="mahasiswa"></div>
                        <div class="cardName">Data Mahasiswa</div>
                    </div>
                    <div class="iconBx">
                        <ion-icon name="people-outline"></ion-icon>
                    </div>
                </div>

                <div class="card" id="card-fakultas">
                    <div>
                        <div class="numbers" id="fakultas"></div>
                        <div class="cardName">Data Fakultas</div>
                    </div>
                    <div class="iconBx">
                        <ion-icon name="people-outline"></ion-icon>
                    </div>
                </div>

                <div class="card" id="card-jurusan">
                    <div>
                        <div class="numbers" id="jurusan"></div>
                        <div class="cardName">Data Jurusan</div>
                    </div>
                    <div class="iconBx">
                        <ion-icon name="people-outline"></ion-icon>
                    </div>
                </div>
            </div>

            <script>
                // Tambahkan event listener ke setiap card
                document.getElementById('card-wisuda').addEventListener('click', function() {
                    window.location.href = '../admin/data_wisuda.php';
                });

                document.getElementById('card-mahasiswa').addEventListener('click', function() {
                    window.location.href = '../admin/data_mahasiswa.php';
                });

                document.getElementById('card-fakultas').addEventListener('click', function() {
                    window.location.href = '../admin/data_fakultas.php';
                });

                document.getElementById('card-jurusan').addEventListener('click', function() {
                    window.location.href = '../admin/data_jurusan.php';
                });

                async function fetchData() {
                    try {
                        const response = await fetch('api.php');  // Sesuaikan path jika berbeda
                        const data = await response.json();
                        
                        console.log('Data API:', data);  // Debugging

                        // Menampilkan data ke elemen HTML
                        document.getElementById('wisuda').textContent = data.wisuda || '0';
                        document.getElementById('mahasiswa').textContent = data.mahasiswa || '0';
                        document.getElementById('fakultas').textContent = data.fakultas || '0';
                        document.getElementById('jurusan').textContent = data.jurusan || '0';
                    } catch (error) {
                        console.error('Gagal mengambil data:', error);
                    }
                }

                // Panggil fetchData saat halaman dimuat
                window.onload = fetchData;
            </script>
        </div>
    </div>

    <!-- =========== Scripts =========  -->
    <script src="../admin/assets/js/main.js"></script>

    <!-- ====== ionicons ======= -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>
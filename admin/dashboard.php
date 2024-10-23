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
                            <img src="../img/logo.png" alt="Logo">
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
                <div class="card">
                    <div>
                        <div class="numbers">100</div>
                        <div class="cardName">Data Wisuda</div>
                    </div>

                    <div class="iconBx">
                        <ion-icon name="people-outline"></ion-icon>
                    </div>
                </div>

                <div class="card">
                    <div>
                        <div class="numbers">200</div>
                        <div class="cardName">Data Mahasiswa</div>
                    </div>

                    <div class="iconBx">
                        <ion-icon name="people-outline"></ion-icon>
                    </div>
                </div>

                <div class="card">
                    <div>
                        <div class="numbers">1</div>
                        <div class="cardName">Data Fakultas</div>
                    </div>

                    <div class="iconBx">
                        <ion-icon name="people-outline"></ion-icon>
                    </div>
                </div>

                <div class="card">
                    <div>
                        <div class="numbers">2</div>
                        <div class="cardName">Data Jurusan</div>
                    </div>

                    <div class="iconBx">
                        <ion-icon name="people-outline"></ion-icon>
                    </div>
                </div>
            </div>

            <!-- ================ List Data Mahasiswa ================= -->
            <div class="details">
                <div class="recentAnnouncements">
                    <div class="cardHeader">
                        <h2>Data Mahasiswa terdaftar</h2>
                        <a href="../admin/data_wisuda.php" class="btn">View All</a>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Nim</th>
                                <th>Fakultas</th>
                                <th>Jurusan</th>
                                <th>Status</th>
                                <th>Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include 'fetch_users.php';

                            if ($result->num_rows > 0) {
                                $no = 1;
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                        <td>" . $no++ . "</td>
                                        <td>" . htmlspecialchars($row['nama']) . "</td>
                                        <td>" . htmlspecialchars($row['npm']) . "</td>
                                        <td>" . htmlspecialchars($row['fakultas']) . "</td>
                                        <td>" . htmlspecialchars($row['jurusan']) . "</td>
                                        <td>" . ($row['is_admin'] ? 'Admin' : 'Mahasiswa') . "</td>
                                        <td>" . htmlspecialchars($row['created_at']) . "</td>
                                        <td class='action-icons'>
                                            <a href='edit.php?id=" . $row['id_users'] . "'>
                                                <i class='fas fa-edit'></i>
                                            </a>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8'>Tidak ada data.</td></tr>";
                            }

                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- ================= List Mahasiswa ================
                <div class="recentCustomers">
                    <div class="cardHeader">
                        <h2>Mahasiswa Terdaftar</h2>
                    </div>

                    <table>
                        <tr>
                            <td width="60px">
                                <div class="imgBx"><img src="assets/imgs/customer02.jpg" alt=""></div>
                            </td>
                            <td>
                                <h4>GIBRAN ALFI ANANTA<br> <span>3222039</span></h4>
                            </td>
                        </tr>

                        <tr>
                            <td width="60px">
                                <div class="imgBx"><img src="assets/imgs/customer06.jpg" alt=""></div>
                            </td>
                            <td>
                                <h4>LEYKA AURA FEBRIANTY<br> <span>3222047</span></h4>
                            </td>
                        </tr>

                        <tr>
                            <td width="60px">
                                <div class="imgBx"><img src="assets/imgs/customer05.jpg" alt=""></div>
                            </td>
                            <td>
                                <h4>DARYAT<br> <span>3222021</span></h4>
                            </td>
                        </tr>

                        <tr>
                            <td width="60px">
                                <div class="imgBx"><img src="assets/imgs/customer07.jpg" alt=""></div>
                            </td>
                            <td>
                                <h4>TIA SETIAWATI<br> <span>3222019</span></h4>
                            </td>
                        </tr>

                        <tr>
                            <td width="60px">
                                <div class="imgBx"><img src="assets/imgs/customer04.jpg" alt=""></div>
                            </td>
                            <td>
                                <h4>NENG NAJWAH<br> <span>3223017</span></h4>
                            </td>
                        </tr>

                    </table>
                </div> -->
            </div>
        </div>
    </div>

    <!-- =========== Scripts =========  -->
    <script src="../admin/assets/js/main.js"></script>

    <!-- ====== ionicons ======= -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>
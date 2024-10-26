<?php
session_start();
require 'admin/db_connnection.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data undangan yang sudah disetujui
$approvedInvitations = $koneksi->query("SELECT * FROM guest WHERE create_by = $user_id AND status = 'Approved'");

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Undangan</title>
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

            <div class="content-container">
                <h2>Kartu Undangan</h2>

                <!-- Tabel untuk undangan yang sudah disetujui -->
                <div class="table-container">
                    <table border="1">
                        <tr>
                            <th>No</th>
                            <th>Kepada</th>
                            <th>Dibuat</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                        <?php
                        $no = 1;
                        while ($row = $approvedInvitations->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td>" . htmlspecialchars($row['kepada']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                            echo "<td><a href='download.php?id_guest=" . $row['id_guest'] . "'>Download</a></td>";
                            echo "</tr>";
                        }
                        ?>
                    </table>
                </div>

                <!-- Tabel untuk mengelola undangan baru -->
                <h2>Daftar Undangan</h2>
                <p><a href="guest_crud.php?action=create">+ Tambah Undangan</a></p>

                <div class="table-container">
                    <table border="1">
                        <tr>
                            <th>No</th>
                            <th>Kepada</th>
                            <th>Dibuat</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                        <?php
                        // Ambil data undangan dari database
                        $allInvitations = $koneksi->query("SELECT * FROM guest WHERE create_by = $user_id");
                        $no = 1;
                        while ($row = $allInvitations->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td>" . htmlspecialchars($row['kepada']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                            echo "<td>
                                <a href='guest_crud.php?action=update&id_guest=" . $row['id_guest'] . "'>Edit</a> |
                                <a href='guest_crud.php?action=delete&id_guest=" . $row['id_guest'] . "'>Hapus</a>";
                            if ($row['status'] == 'Pending') {
                                echo " | <a href='guest_crud.php?action=approve&id_guest=" . $row['id_guest'] . "'>Setujui</a>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </table>
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

<?php $koneksi->close(); ?>

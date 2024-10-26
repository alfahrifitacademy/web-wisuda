<?php
session_start();
require 'admin/db_connnection.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Cek status dokumen di tabel `dokumen`
$documentStatusQuery = $koneksi->query("SELECT status FROM dokumen WHERE create_by = $user_id");
$documentStatusRow = $documentStatusQuery->fetch_assoc();
$documentStatus = $documentStatusRow['status'];

// Cek apakah dokumen disetujui
$isApproved = ($documentStatus == 'Approved');

// Jika dokumen sudah disetujui, pindahkan data dari `guest` ke `kartu_undangan`
if ($isApproved) {
    // Ambil data undangan dari `guest`
    $guestDataQuery = $koneksi->query("SELECT * FROM guest WHERE create_by = $user_id");
    while ($guestRow = $guestDataQuery->fetch_assoc()) {
        // Insert data ke tabel `kartu_undangan`
        $stmt = $koneksi->prepare("INSERT INTO kartu_undangan (kepada, create_by, created_at, status) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siss", $guestRow['kepada'], $guestRow['create_by'], $guestRow['created_at'], $guestRow['status']);
        $stmt->execute();
        $stmt->close();
    }

    // Hapus data dari tabel `guest`
    $koneksi->query("DELETE FROM guest WHERE create_by = $user_id");
}

// Ambil data undangan dari tabel yang sesuai
$undanganQuery = $isApproved ? "SELECT * FROM kartu_undangan WHERE create_by = $user_id" : "SELECT * FROM guest WHERE create_by = $user_id";
$undanganData = $koneksi->query($undanganQuery);

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
                        while ($row = $undanganData->fetch_assoc()) {
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
                <!-- Tampilkan tombol tambah undangan hanya jika dokumen belum disetujui -->
                <?php if (!$isApproved): ?>
                    <p><a href="guest_crud.php?action=create">+ Tambah Undangan</a></p>
                <?php else: ?>
                    <p style="color: red;">Dokumen Anda telah disetujui. Anda tidak bisa menambahkan undangan lagi.</p>
                <?php endif; ?>

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
                        while ($row = $undanganData->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td>" . htmlspecialchars($row['kepada']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['status']) . "</td>";

                            // Jika dokumen sudah disetujui, tampilkan tombol download
                            if ($isApproved) {
                                echo "<td><a href='download.php?id_kartu=" . $row['id_kartu'] . "'>Download</a></td>";
                            } else {
                                // Jika dokumen belum disetujui, hanya tampilkan tombol edit dan hapus
                                echo "<td>
                                    <a href='guest_crud.php?action=update&id_guest=" . $row['id_guest'] . "'>Edit</a> |
                                    <a href='guest_crud.php?action=delete&id_guest=" . $row['id_guest'] . "'>Hapus</a>
                                </td>";
                            }
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

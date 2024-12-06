<?php
session_start();
require 'admin/db_connnection.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Query untuk menampilkan dokumen yang sudah disetujui (approved) milik pengguna yang login
$query = "
    SELECT users.id_users, users.nama, users.nim, dokumen.status 
    FROM users
    JOIN dokumen ON users.id_users = dokumen.create_by
    WHERE dokumen.status = 'approved' AND dokumen.create_by = $user_id
";

$result = $koneksi->query($query);

// Query untuk mengambil status dokumen pengguna
$doc_status_query = "
    SELECT status 
    FROM dokumen 
    WHERE create_by = $user_id
";
$doc_status_result = $koneksi->query($doc_status_query);
$doc_status = '';

// Cek jika status dokumen ditemukan
if ($doc_status_result->num_rows > 0) {
    $doc_status_row = $doc_status_result->fetch_assoc();
    $doc_status = $doc_status_row['status'];
}

// Jika status dokumen adalah 'approved', update status undangan
if ($doc_status == 'approved') {
    $update_invitation_query = "
        UPDATE guest 
        SET status = 'Approved' 
        WHERE create_by = $user_id AND status != 'Approved'
    ";
    $koneksi->query($update_invitation_query);
}

// Query untuk menampilkan daftar undangan yang dibuat oleh pengguna
$query = "
    SELECT id_guest, kepada, created_at, status 
    FROM guest 
    WHERE create_by = $user_id
";
$invitations_result = $koneksi->query($query);
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

            <h2>Kartu Undangan</h2>
            <div class="table-container">
                <table border="1">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>NIM</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                    <?php
                    // Menampilkan data untuk pengguna yang status dokumennya 'approved'
                    $no = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $no++ . "</td>";
                        echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['nim']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['status']) . "</td>"; // Menampilkan status dokumen
                        echo "<td>";
                        // Menampilkan link download hanya jika statusnya 'approved'
                        if ($row['status'] == 'approved') {
                            echo "<a href='download.php?id_users=" . $row['id_users'] . "'>Download Surat</a>";
                        } else {
                            echo "Tidak tersedia"; // Jika status selain 'approved', tampilkan teks ini
                        }
                        echo "</td>";
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
                    // Menampilkan data undangan yang terkait dengan user yang login
                    $no = 1;
                    while ($row = $invitations_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $no++ . "</td>";
                        echo "<td>" . htmlspecialchars($row['kepada']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                        echo "<td>";
                        
                        // Aksi berdasarkan status undangan
                        if ($row['status'] == 'Approved') {
                            echo "<a href='download_guest.php?id_guest=" . $row['id_guest'] . "'>Download Surat</a>";
                        } else {
                            echo "<a href='guest_crud.php?action=update&id_guest=" . $row['id_guest'] . "'>Edit</a> | ";
                            echo "<a href='guest_crud.php?action=delete&id_guest=" . $row['id_guest'] . "'>Hapus</a>";
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
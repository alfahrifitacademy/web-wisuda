<?php
session_start();

// Periksa apakah admin sudah login
if (!isset($_SESSION['admin'])) {
    header("Location: login.php"); // Jika belum login, kembali ke halaman login
    exit;
}

// Hubungkan ke database
include '../admin/db_connnection.php';

// Query utama untuk mendapatkan data mahasiswa
$users = mysqli_query($koneksi, "
    SELECT u.id_users, u.nama, u.nim, IFNULL(f.fakultas, 'Tidak Tersedia') AS fakultas, 
           IFNULL(j.jurusan, 'Tidak Tersedia') AS jurusan, 
           (SELECT status FROM dokumen WHERE create_by = u.id_users ORDER BY created_at DESC LIMIT 1) AS status, 
           u.created_at
    FROM users u
    LEFT JOIN fakultas f ON u.fakultas = f.id_fakultas
    LEFT JOIN jurusan j ON u.jurusan = j.id_jurusan
    ORDER BY u.id_users ASC
");

// Ambil data dokumen dengan join ke tabel users
$dokumen_data = mysqli_query($koneksi, "
    SELECT d.*, u.nama AS create_by_name 
    FROM dokumen d 
    JOIN users u ON d.create_by = u.id_users 
    ORDER BY d.created_at DESC
");

// Cek berkas (ambil data dokumen berdasarkan id_users)
function getDokumenData($koneksi, $id_users)
{
    $result = mysqli_query($koneksi, "
        SELECT id_dokumen, file_akte, file_ijazah, file_pembayaran, status, tanggal_wisuda, waktu_wisuda
        FROM dokumen
        WHERE create_by = '$id_users'
    ");
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Simpan perubahan status dokumen
if (isset($_POST['simpan_status'])) {
    $dokumen_ids = $_POST['dokumen_ids'];
    $statuses = $_POST['statuses'];

    foreach ($dokumen_ids as $index => $id_dokumen) {
        $status = $statuses[$index];
        mysqli_query($koneksi, "UPDATE dokumen SET status='$status' WHERE id_dokumen='$id_dokumen'");
    }
    echo "<script>alert('Status berhasil disimpan');</script>";
    header("Location: data_wisuda.php");
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
                    <a href="../admin/dashboard.php">
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
                    <a href="../admin/pengumuman.php">
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

            <div class="containerTable">
                <h2>Tabel Data Wisuda</h2>
                <table id="wisudaTable" class="table-wisuda">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>NIM</th>
                            <th>Fakultas</th>
                            <th>Jurusan</th>
                            <th>Status</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1;
                        while ($user = mysqli_fetch_assoc($users)) {
                            // Tentukan kelas CSS untuk status berdasarkan status saat ini
                            $status_class = ($user['status'] == 'Approve') ? 'status-approve' : (($user['status'] == 'Reject') ? 'status-reject' : 'status-pending');
                        ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td><?= $user['nama'] ?? 'Tidak tersedia'; ?></td>
                                <td><?= $user['nim'] ?? 'Tidak tersedia'; ?></td>
                                <td><?= $user['fakultas'] ?? 'Tidak tersedia'; ?></td>
                                <td><?= $user['jurusan'] ?? 'Tidak tersedia'; ?></td>
                                <td>
                                    <span class="status-badge <?= $status_class ?>">
                                        <?= $user['status'] ?? 'Data Kosong'; ?>
                                    </span>
                                </td>
                                <td><?= $user['created_at'] ?? 'Tidak tersedia'; ?></td>
                                <td><a href="detail_dokumen.php?id_users=<?= $user['id_users']; ?>" class="btn-cek">Cek Berkas</a></td>
                            </tr>
                        <?php } ?>
                    </tbody>

                </table>
            </div>

            <script>
                // Fungsi untuk update status langsung dari tombol
                function updateStatus(id_users, status) {
                    fetch('update_status.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                id_users,
                                status
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Status berhasil diperbarui');
                                location.reload(); // Muat ulang halaman untuk memperbarui tampilan status
                            } else {
                                alert('Gagal memperbarui status');
                            }
                        })
                        .catch(error => console.error('Error:', error));
                }
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
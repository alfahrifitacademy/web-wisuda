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
    SELECT u.id_users, u.nama, u.nim, 
           IFNULL(f.fakultas, 'Tidak Tersedia') AS fakultas, 
           IFNULL(j.jurusan, 'Tidak Tersedia') AS jurusan, 
           (SELECT status FROM dokumen WHERE create_by = u.id_users ORDER BY created_at DESC LIMIT 1) AS status, 
           u.created_at
    FROM users u
    LEFT JOIN fakultas f ON u.fakultas = f.id_fakultas
    LEFT JOIN jurusan j ON u.jurusan = j.id_jurusan
    ORDER BY u.id_users ASC
");

// Query untuk mengambil data dokumen dengan join ke tabel users
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
        SELECT id_dokumen, file_akte, file_ijazah, file_pembayaran, status, tgl_wisuda, waktu
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

// Ambil data pengguna yang berstatus 'approved', 'rejected', atau 'pending'
$users_query = "SELECT u.id_users, u.nama, u.nim, f.fakultas, j.jurusan, u.created_at, d.status, d.tgl_wisuda, d.waktu
                FROM users u
                LEFT JOIN fakultas f ON u.fakultas = f.id_fakultas
                LEFT JOIN jurusan j ON u.jurusan = j.id_jurusan
                LEFT JOIN dokumen d ON u.id_users = d.create_by";
$users = mysqli_query($koneksi, $users_query);

// Update tgl_wisuda dan waktu untuk semua pengguna yang meng-upload dokumen
if (isset($_POST['update_wisuda'])) {
    $tgl_wisuda = $_POST['tgl_wisuda'];
    $waktu = $_POST['waktu'];

    // Update tgl_wisuda dan waktu pada dokumen untuk semua pengguna
    $update_query = "UPDATE dokumen SET tgl_wisuda='$tgl_wisuda', waktu='$waktu' WHERE tgl_wisuda IS NULL OR waktu IS NULL";
    mysqli_query($koneksi, $update_query);

    echo "<script>alert('Tanggal Wisuda dan Waktu berhasil diperbarui');</script>";
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

                <!-- Tombol untuk menambah periode/tanggal wisuda -->
                <button class="btn-tambah" data-bs-toggle="modal" data-bs-target="#wisudaModal">Set Tanggal Wisuda & Waktu</button>

                <!-- Modal untuk menambah Tanggal Wisuda dan Waktu -->
                <div class="modal" id="wisudaModal" tabindex="-1" aria-labelledby="wisudaModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="wisudaModalLabel">Tambah Periode Wisuda</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="data_wisuda.php" method="POST">
                                <div class="modal-body">
                                    <label for="tgl_wisuda">Tanggal Wisuda:</label>
                                    <input type="date" name="tgl_wisuda" required class="form-control">
                                    <label for="waktu">Waktu Wisuda:</label>
                                    <input type="time" name="waktu" required class="form-control">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    <button type="submit" name="update_wisuda" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


                <table id="wisudaTable" class="table-wisuda">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>NIM</th>
                            <th>Fakultas</th>
                            <th>Jurusan</th>
                            <th>Status</th>
                            <th>Tanggal Wisuda</th>
                            <th>Waktu</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php while ($user = mysqli_fetch_assoc($users)) {
                            // Tentukan kelas CSS untuk status berdasarkan status saat ini
                            $status_class = '';
                            switch ($user['status']) {
                                case 'approved':
                                    $status_class = 'status-approve'; // CSS untuk status approve
                                    break;
                                case 'rejected':
                                    $status_class = 'status-reject'; // CSS untuk status reject
                                    break;
                                case 'pending':
                                default:
                                    $status_class = 'status-pending'; // CSS untuk status pending
                                    break;
                            }
                        ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td><?= htmlspecialchars($user['nama'] ?? 'Tidak tersedia'); ?></td>
                                <td><?= htmlspecialchars($user['nim'] ?? 'Tidak tersedia'); ?></td>
                                <td><?= htmlspecialchars($user['fakultas'] ?? 'Tidak tersedia'); ?></td>
                                <td><?= htmlspecialchars($user['jurusan'] ?? 'Tidak tersedia'); ?></td>
                                <td>
                                    <span class="status-badge <?= $status_class ?>">
                                        <?= htmlspecialchars($user['status'] ?? 'Data Kosong'); ?>
                                    </span>
                                </td>
                                <td>
                                    <?= !empty($user['tgl_wisuda']) ? date("d F Y", strtotime($user['tgl_wisuda'])) : 'Belum ditentukan'; ?>
                                </td>
                                <td>
                                    <?= !empty($user['waktu']) ? date("H:i", strtotime($user['waktu'])) : 'Belum ditentukan'; ?>
                                </td>
                                <td><?= htmlspecialchars($user['created_at'] ?? 'Tidak tersedia'); ?></td>
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


    <!-- Bootstrap 5 JS (untuk modal) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ====== ionicons ======= -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>
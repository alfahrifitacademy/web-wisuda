<?php
session_start();

// Periksa apakah admin sudah login
if (!isset($_SESSION['admin'])) {
    header("Location: login.php"); // Jika belum login, kembali ke halaman login
    exit;
}

// Hubungkan ke database
include '../admin/db_connnection.php';

// Ambil data dokumen dengan join ke tabel users
$dokumen_data = mysqli_query($koneksi, "
    SELECT d.*, u.nama AS create_by_name 
    FROM dokumen d 
    JOIN users u ON d.create_by = u.id_users 
    ORDER BY d.created_at DESC
");

// Tambah data dokumen
if (isset($_POST['tambah_dokumen'])) {
    $file_akte = $_POST['file_akte'];
    $file_ijasa = $_POST['file_ijasa'];
    $file_pembayaran = $_POST['file_pembayaran'];
    $created_by = $_POST['created_by'];
    $created_at = date("Y-m-d H:i:s");

    $query = "INSERT INTO dokumen (file_akte, file_ijasa, file_pembayaran, create_by, created_at, status) 
              VALUES ('$file_akte', '$file_ijasa', '$file_pembayaran', '$created_by', '$created_at', 'pending')";
    mysqli_query($koneksi, $query);
    header('Location: data_wisuda.php');
}

// Update data dokumen
if (isset($_POST['update_dokumen'])) {
    $id_dok = $_POST['id_dok'];
    $status = $_POST['status'];
    $tgl_wisuda = $_POST['tgl_wisuda'];
    $waktu = $_POST['waktu'];

    // Query untuk memperbarui data dokumen
    $query = "UPDATE dokumen SET status='$status', tgl_wisuda='$tgl_wisuda', waktu='$waktu' WHERE id_dok='$id_dok'";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data dokumen berhasil diperbarui');</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data dokumen');</script>";
    }
    header('Location: data_wisuda.php');
    exit;
}


// Hapus data dokumen
if (isset($_GET['hapus_dokumen'])) {
    $id_dok = $_GET['hapus_dokumen'];

    // Query DELETE untuk menghapus data dokumen
    $query = "DELETE FROM dokumen WHERE id_dok='$id_dok'";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Dokumen berhasil dihapus');</script>";
    } else {
        echo "<script>alert('Gagal menghapus dokumen');</script>";
    }

    header('Location: data_wisuda.php');
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
                            <th>File Akte</th>
                            <th>File Ijasah</th>
                            <th>File Pembayaran</th>
                            <th>Status</th>
                            <th>Tanggal Wisuda</th>
                            <th>Waktu Wisuda</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1;
                        while ($dokumen = mysqli_fetch_assoc($dokumen_data)) { ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td><?= $dokumen['create_by_name']; ?></td>

                                <!-- File Akte -->
                                <td>
                                    <a href="../uploads/<?= $dokumen['file_akte']; ?>" target="_blank">
                                        <button class="btn-berkas">
                                            <i class="fas fa-file-alt"></i> Akte
                                        </button>
                                    </a>
                                </td>

                                <!-- File Ijasah -->
                                <td>
                                    <a href="../uploads/<?= $dokumen['file_ijasa']; ?>" target="_blank">
                                        <button class="btn-berkas">
                                            <i class="fas fa-file-alt"></i> Ijasah
                                        </button>
                                    </a>
                                </td>

                                <!-- File Pembayaran -->
                                <td>
                                    <a href="../uploads/<?= $dokumen['file_pembayaran']; ?>" target="_blank">
                                        <button class="btn-berkas">
                                            <i class="fas fa-file-image"></i> Pembayaran
                                        </button>
                                    </a>
                                </td>

                                <!-- Status dengan ikon jam atau centang -->
                                <td>
                                    <?php if ($dokumen['status'] == 'pending') { ?>
                                        <i class="fas fa-clock text-pending"></i> Pending
                                    <?php } else { ?>
                                        <i class="fas fa-check-circle text-approve"></i> Approve
                                    <?php } ?>
                                </td>

                                <!-- Tanggal dan Waktu Wisuda -->
                                <td><?= $dokumen['tgl_wisuda']; ?></td>
                                <td><?= $dokumen['waktu']; ?></td>

                                <!-- Aksi -->
                                <!-- Aksi -->
                                <td class="aksi">
                                    <button class="btn-hapus" onclick="hapusDokumen(<?= $dokumen['id_dok']; ?>)">Hapus</button>
                                    <button class="btn-edit" onclick="editDokumen(<?= $dokumen['id_dok']; ?>, '<?= $dokumen['status']; ?>', '<?= $dokumen['tgl_wisuda']; ?>', '<?= $dokumen['waktu']; ?>')">Edit</button>
                                </td>

                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <!-- Form Edit Dokumen -->
                <div id="editForm" class="form-modal" style="display: none;">
                    <button class="close-icon" onclick="closeForm('editForm')">&times;</button>
                    <form method="POST">
                        <h3>Edit Data Wisuda</h3>
                        <input type="hidden" name="id_dok" id="editId">

                        <!-- Dropdown Status -->
                        <label for="status">Status:</label>
                        <select name="status" id="editStatus" required>
                            <option value="pending">Pending</option>
                            <option value="approve">Approve</option>
                        </select>

                        <!-- Input Tanggal dan Waktu Wisuda -->
                        <label for="tgl_wisuda">Tanggal Wisuda:</label>
                        <input type="date" name="tgl_wisuda" id="editTglWisuda" required>

                        <label for="waktu">Waktu Wisuda:</label>
                        <input type="time" name="waktu" id="editWaktu" required>

                        <button type="submit" name="update_dokumen">Update</button>
                    </form>
                </div>



                <!-- Overlay -->
                <div id="overlay" onclick="closeOverlay()" style="display:none;"></div>

                <script>
                    function openForm(formId) {
                        document.getElementById(formId).style.display = 'block';
                        document.getElementById('overlay').style.display = 'block';
                    }

                    function closeForm(formId) {
                        document.getElementById(formId).style.display = 'none';
                        document.getElementById('overlay').style.display = 'none';
                    }

                    function closeOverlay() {
                        document.getElementById('editForm').style.display = 'none';
                        document.getElementById('overlay').style.display = 'none';
                    }

                    function editDokumen(id, status, tgl_wisuda, waktu) {
                        document.getElementById('editId').value = id;
                        document.getElementById('editStatus').value = status;
                        document.getElementById('editTglWisuda').value = tgl_wisuda;
                        document.getElementById('editWaktu').value = waktu;
                        openForm('editForm');
                    }

                    function hapusDokumen(id) {
                        if (confirm('Apakah Anda yakin ingin menghapus dokumen ini?')) {
                            window.location.href = '?hapus_dokumen=' + id;
                        }
                    }
                </script>
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
<?php
session_start();

// Periksa apakah admin sudah login
if (!isset($_SESSION['admin'])) {
    header("Location: login.php"); // Jika belum login, kembali ke halaman login
    exit;
}

// Hubungkan ke database
include '../admin/db_connnection.php';

// Ambil data pengumuman
$pengumuman_data = mysqli_query($koneksi, "SELECT * FROM pengumuman ORDER BY created_at DESC");

// Tambah data pengumuman
if (isset($_POST['tambah_pengumuman'])) {
    $judul = $_POST['judul'];
    $pengumuman = $_POST['pengumuman'];
    $created_at = date("Y-m-d H:i:s");

    $query = "INSERT INTO pengumuman (judul, pengumuman, created_at) VALUES ('$judul', '$pengumuman', '$created_at')";
    mysqli_query($koneksi, $query);
    header('Location: pengumuman.php');
}

// Update data pengumuman
if (isset($_POST['update_pengumuman'])) {
    $id_pengumuman = $_POST['id_pengumuman'];
    $judul = $_POST['judul'];
    $pengumuman = $_POST['pengumuman'];

    $query = "UPDATE pengumuman SET judul='$judul', pengumuman='$pengumuman' WHERE id_pengumuman='$id_pengumuman'";
    mysqli_query($koneksi, $query);
    header('Location: pengumuman.php');
}

// Hapus data pengumuman
if (isset($_GET['hapus_pengumuman'])) {
    $id_pengumuman = $_GET['hapus_pengumuman'];

    // Query DELETE untuk menghapus pengumuman berdasarkan id_pengumuman
    $query = "DELETE FROM pengumuman WHERE id_pengumuman='$id_pengumuman'";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Pengumuman berhasil dihapus');</script>";
    } else {
        echo "<script>alert('Gagal menghapus pengumuman');</script>";
    }

    // Refresh halaman setelah penghapusan
    header('Location: pengumuman.php');
    exit;
}

// Ambil data admin dari database berdasarkan admin_id di session
$admin_id = $_SESSION['admin'];
$query = "SELECT photo FROM admin WHERE id_admin = '$admin_id'";
$result = mysqli_query($koneksi, $query);
$admin = mysqli_fetch_assoc($result);

// Tentukan path foto profil atau default
$foto_profile = !empty($admin['photo']) && file_exists("../" . $admin['photo'])
    ? "../" . $admin['photo']
    : "/web-wisuda2/assets/img/default-profile.svg";
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
                    <a href="../admin/pengumuman.php">
                        <span class="icon">
                            <ion-icon name="chatbubble-outline"></ion-icon>
                        </span>
                        <span class="title">pengumuman</span>
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
                    <a href="profile_settings.php">
                        <img src="<?= $foto_profile; ?>" alt="Foto Profil" style="cursor: pointer; width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                    </a>
                </div>
            </div>

            <div class="containerTable">
                <h2>Tabel Pengumuman</h2>
                <button id="tambahpengumuman" onclick="openForm('tambahForm')">Tambah Pengumuman</button>

                <table id="pengumumanTable" class="table-pengumuman">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul</th>
                            <th>Pengumuman</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1;
                        while ($pengumuman = mysqli_fetch_assoc($pengumuman_data)) { ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td><?= $pengumuman['judul']; ?></td>
                                <!-- Tampilkan isi pengumuman secara penuh -->
                                <td class="pengumuman-content"><?= nl2br($pengumuman['pengumuman']); ?></td>
                                <td class="aksi">
                                    <button class="btn-hapus" onclick="hapusPengumuman(<?= $pengumuman['id_pengumuman']; ?>)">Hapus</button>
                                    <button class="btn-edit" onclick="editpengumuman(<?= $pengumuman['id_pengumuman']; ?>, '<?= $pengumuman['judul']; ?>', '<?= addslashes($pengumuman['pengumuman']); ?>')">Edit</button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>

                </table>

                <!-- Form Tambah Pengumuman -->
                <div id="tambahForm" class="form-modal">
                    <!-- <button class="close-icon" onclick="closeForm('tambahForm')">&times;</button> -->
                    <form method="POST">
                        <h3>Tambah Pengumuman</h3>
                        <input type="text" name="judul" placeholder="Judul Pengumuman" required>
                        <textarea name="pengumuman" placeholder="Isi Pengumuman" rows="4" required></textarea>
                        <button type="submit" name="tambah_pengumuman">Tambah</button>
                    </form>
                </div>

                <!-- Form Edit Pengumuman -->
                <div id="editForm" class="form-modal">
                    <!-- <button class="close-icon" onclick="closeForm('editForm')">&times;</button> -->
                    <form method="POST">
                        <h3>Edit Pengumuman</h3>
                        <input type="hidden" name="id_pengumuman" id="editId">
                        <input type="text" name="judul" id="editJudul" placeholder="Judul Pengumuman" required>
                        <textarea name="pengumuman" id="editpengumuman" placeholder="Isi Pengumuman" rows="4" required></textarea>
                        <button type="submit" name="update_pengumuman">Update</button>
                    </form>
                </div>

                <!-- Overlay -->
                <div id="overlay" onclick="closeOverlay()" style="display:none;"></div>

                <script>
                    // Fungsi untuk membuka dan menutup form
                    function openForm(formId) {
                        document.getElementById(formId).style.display = 'block';
                        document.getElementById('overlay').style.display = 'block';
                    }

                    function closeForm(formId) {
                        document.getElementById(formId).style.display = 'none';
                        document.getElementById('overlay').style.display = 'none';
                    }

                    function closeOverlay() {
                        document.getElementById('tambahForm').style.display = 'none';
                        document.getElementById('editForm').style.display = 'none';
                        document.getElementById('overlay').style.display = 'none';
                    }

                    // Fungsi untuk membuka form edit dan mengisi data pengumuman yang dipilih
                    function editpengumuman(id, judul, pengumuman) {
                        document.getElementById('editId').value = id;
                        document.getElementById('editJudul').value = judul;
                        document.getElementById('editpengumuman').value = pengumuman;
                        openForm('editForm');
                    }
                    // Fungsi untuk menghapus pengumuman dengan konfirmasi
                    function hapusPengumuman(id) {
                        if (confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')) {
                            // Redirect ke URL dengan parameter hapus_pengumuman
                            window.location.href = '?hapus_pengumuman=' + id;
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
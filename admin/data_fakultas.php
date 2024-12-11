<?php
session_start();
// Hubungkan ke database
include '../admin/db_connnection.php';

// Periksa apakah admin sudah login
if (!isset($_SESSION['admin'])) {
    header("Location: login.php"); // Jika belum login, kembali ke halaman login
    exit;
}

// Ambil data fakultas
$fakultas_data = mysqli_query($koneksi, "SELECT * FROM fakultas");

// Tambah data fakultas
if (isset($_POST['tambah_fakultas'])) {
    $fakultas = $_POST['fakultas'];
    $query = "INSERT INTO fakultas (fakultas) VALUES ('$fakultas')";
    mysqli_query($koneksi, $query);
    header('Location: data_fakultas.php');
}

// Update data fakultas
if (isset($_POST['update_fakultas'])) {
    $id_fakultas = $_POST['id_fakultas'];
    $fakultas = $_POST['fakultas'];
    $query = "UPDATE fakultas SET fakultas='$fakultas' WHERE id_fakultas='$id_fakultas'";
    mysqli_query($koneksi, $query);
    header('Location: data_fakultas.php');
}

// Hapus data fakultas
if (isset($_GET['hapus_fakultas'])) {
    $id_fakultas = $_GET['hapus_fakultas'];

    // Query DELETE untuk menghapus data fakultas
    $query = "DELETE FROM fakultas WHERE id_fakultas='$id_fakultas'";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Fakultas berhasil dihapus');</script>";
    } else {
        echo "<script>alert('Gagal menghapus fakultas');</script>";
    }

    // Refresh halaman setelah penghapusan
    header('Location: data_fakultas.php');
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
                    <a href="profile_settings.php">
                        <img src="<?= $foto_profile; ?>" alt="Foto Profil" style="cursor: pointer; width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                    </a>
                </div>
            </div>

            <div class="containerTable">
                <h2>Tabel Fakultas</h2>
                <!-- Tombol Tambah Fakultas -->
                <button id="tambahFakultas" onclick="openForm('tambahForm')">Tambah Fakultas</button>

                <table id="fakultasTable" class="table-fakultas">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Fakultas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1;
                        while ($fakultas = mysqli_fetch_assoc($fakultas_data)) { ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td><?= $fakultas['fakultas']; ?></td>
                                <td class="aksi">
                                    <button class="btn-edit" onclick="editFakultas(<?= $fakultas['id_fakultas']; ?>, '<?= addslashes($fakultas['fakultas']); ?>')">Edit</button>
                                    <button class="btn-hapus" onclick="hapusFakultas(<?= $fakultas['id_fakultas']; ?>)">Hapus</button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <!-- Form Tambah Fakultas -->
                <div id="tambahForm" class="form-modal" style="display: none;">
                    <form method="POST">
                        <h3>Tambah Fakultas</h3>
                        <input type="text" name="fakultas" placeholder="Nama Fakultas" required>
                        <button type="submit" name="tambah_fakultas">Tambah</button>
                    </form>
                </div>

                <!-- Form Edit Fakultas -->
                <div id="editForm" class="form-modal" style="display: none;">
                    <form method="POST">
                        <h3>Edit Fakultas</h3>
                        <input type="hidden" name="id_fakultas" id="editId">
                        <input type="text" name="fakultas" id="editNamaFakultas" placeholder="Nama Fakultas" required>
                        <button type="submit" name="update_fakultas">Update</button>
                    </form>
                </div>

                <!-- Overlay -->
                <div id="overlay" onclick="closeOverlay()" style="display: none;"></div>
            </div>

            <script>
                // Fungsi untuk membuka form
                function openForm(formId) {
                    document.getElementById(formId).style.display = 'block';
                    document.getElementById('overlay').style.display = 'block';
                }

                // Fungsi untuk menutup form
                function closeForm(formId) {
                    document.getElementById(formId).style.display = 'none';
                    document.getElementById('overlay').style.display = 'none';
                }

                // Fungsi untuk menutup overlay
                function closeOverlay() {
                    document.getElementById('tambahForm').style.display = 'none';
                    document.getElementById('editForm').style.display = 'none';
                    document.getElementById('overlay').style.display = 'none';
                }

                // Fungsi untuk membuka form edit dengan data yang dipilih
                function editFakultas(id, nama) {
                    document.getElementById('editId').value = id;
                    document.getElementById('editNamaFakultas').value = nama;
                    openForm('editForm');
                }

                // Fungsi untuk menghapus fakultas dengan konfirmasi
                function hapusFakultas(id) {
                    if (confirm('Apakah Anda yakin ingin menghapus fakultas ini?')) {
                        // Redirect ke URL dengan parameter hapus_fakultas
                        window.location.href = '?hapus_fakultas=' + id;
                    }
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
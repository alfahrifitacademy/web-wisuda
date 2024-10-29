<?php
session_start();

// Periksa apakah admin sudah login
if (!isset($_SESSION['admin'])) {
    header("Location: login.php"); // Jika belum login, kembali ke halaman login
    exit;
}

// Hubungkan ke database
include '../admin/db_connnection.php';

// Ambil data jurusan
$jurusan_data = mysqli_query($koneksi, "SELECT j.id_jurusan, j.jurusan, f.fakultas FROM jurusan j JOIN fakultas f ON j.fakultas_id = f.id_fakultas");

// Tambah data jurusan
if (isset($_POST['tambah_jurusan'])) {
    $jurusan = $_POST['jurusan'];
    $fakultas_id = $_POST['fakultas_id'];

    // Pastikan fakultas_id tidak kosong
    if (!empty($fakultas_id)) {
        $query = "INSERT INTO jurusan (jurusan, fakultas_id) VALUES ('$jurusan', '$fakultas_id')";
        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('Jurusan berhasil ditambahkan');</script>";
        } else {
            echo "<script>alert('Gagal menambahkan jurusan');</script>";
        }
    } else {
        echo "<script>alert('Pilih fakultas terlebih dahulu');</script>";
    }
    header('Location: data_jurusan.php');
    exit;
}


// Ambil data jurusan dengan join ke tabel fakultas
$jurusan_data = mysqli_query($koneksi, "
    SELECT j.id_jurusan, j.jurusan, j.fakultas_id, f.fakultas
    FROM jurusan j
    JOIN fakultas f ON j.fakultas_id = f.id_fakultas
    ORDER BY j.id_jurusan ASC
");


// Update data jurusan
if (isset($_POST['update_jurusan'])) {
    $id_jurusan = $_POST['id_jurusan'];
    $jurusan = $_POST['jurusan'];
    $fakultas_id = $_POST['fakultas_id'];

    // Validasi fakultas_id tidak kosong
    if (!empty($fakultas_id)) {
        $query = "UPDATE jurusan SET jurusan='$jurusan', fakultas_id='$fakultas_id' WHERE id_jurusan='$id_jurusan'";
        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('Jurusan berhasil diperbarui');</script>";
        } else {
            echo "<script>alert('Gagal memperbarui jurusan');</script>";
        }
    } else {
        echo "<script>alert('Pilih fakultas terlebih dahulu');</script>";
    }

    // Refresh halaman setelah update
    header('Location: data_jurusan.php');
    exit;
}


// Hapus data jurusan
if (isset($_GET['hapus_jurusan'])) {
    $id_jurusan = $_GET['hapus_jurusan'];

    // Query DELETE untuk menghapus data jurusan
    $query = "DELETE FROM jurusan WHERE id_jurusan='$id_jurusan'";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Jurusan berhasil dihapus');</script>";
    } else {
        echo "<script>alert('Gagal menghapus jurusan');</script>";
    }

    // Refresh halaman setelah penghapusan
    header('Location: data_jurusan.php');
    exit;
}

// Ambil data fakultas untuk dropdown
$fakultas_data = mysqli_query($koneksi, "SELECT * FROM fakultas");
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
                    <img src="assets/imgs/customer01.jpg" alt="pp">
                </div>
            </div>

            <div class="containerTable">
                <h2>Tabel Jurusan</h2>
                <!-- Tombol Tambah Jurusan -->
                <button id="tambahJurusan" onclick="openForm('tambahForm')">Tambah Jurusan</button>
                <table id="jurusanTable" class="table-jurusan">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Jurusan</th>
                            <th>Fakultas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1;
                        while ($jurusan = mysqli_fetch_assoc($jurusan_data)) { ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td><?= $jurusan['jurusan']; ?></td>
                                <td><?= $jurusan['fakultas']; ?></td>
                                <td class="aksi">
                                    <button class="btn-edit" onclick="editJurusan(<?= $jurusan['id_jurusan']; ?>, '<?= addslashes($jurusan['jurusan']); ?>')">Edit</button>
                                    <button class="btn-hapus" onclick="hapusJurusan(<?= $jurusan['id_jurusan']; ?>)">Hapus</button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <!-- Form Tambah Jurusan -->
                <div id="tambahForm" class="form-modal" style="display: none;">
                    <!-- <button class="close-icon" onclick="closeForm('tambahForm')">&times;</button> -->
                    <form method="POST">
                        <h3>Tambah Jurusan</h3>
                        <input type="text" name="jurusan" placeholder="Nama Jurusan" required>
                        <select name="fakultas_id" required>
                            <option value="">Pilih Fakultas</option>
                            <?php
                            // Ambil data fakultas
                            $fakultas_data = mysqli_query($koneksi, "SELECT * FROM fakultas");
                            while ($fakultas = mysqli_fetch_assoc($fakultas_data)) { ?>
                                <option value="<?= $fakultas['id_fakultas']; ?>"><?= $fakultas['fakultas']; ?></option>
                            <?php } ?>
                        </select>
                        <button type="submit" name="tambah_jurusan">Tambah</button>
                    </form>
                </div>

                <!-- Form Edit Jurusan -->
                <div id="editForm" class="form-modal" style="display: none;">
                    <!-- <button class="close-icon" onclick="closeForm('editForm')">&times;</button> -->
                    <form method="POST">
                        <h3>Edit Jurusan</h3>
                        <input type="hidden" name="id_jurusan" id="editId">
                        <input type="text" name="jurusan" id="editNamaJurusan" placeholder="Nama Jurusan" required>
                        <select name="fakultas_id" id="editFakultasId" required>
                            <option value="">Pilih Fakultas</option>
                            <?php
                            // Ambil data fakultas untuk dropdown
                            $fakultas_data = mysqli_query($koneksi, "SELECT * FROM fakultas");
                            while ($fakultas = mysqli_fetch_assoc($fakultas_data)) { ?>
                                <option value="<?= $fakultas['id_fakultas']; ?>"><?= $fakultas['fakultas']; ?></option>
                            <?php } ?>
                        </select>
                        <button type="submit" name="update_jurusan">Update</button>
                    </form>
                </div>


                <!-- Overlay -->
                <div id="overlay" onclick="closeOverlay()" style="display: none;"></div>
            </div>


            <script>
                // Fungsi untuk membuka form edit dengan data yang dipilih
                function editJurusan(id, namaJurusan, fakultasId) {
                    // Isi nilai input form edit
                    document.getElementById('editId').value = id;
                    document.getElementById('editNamaJurusan').value = namaJurusan;

                    // Mengatur pilihan fakultas di dropdown form edit
                    var fakultasSelect = document.getElementById('editFakultasId');
                    for (var i = 0; i < fakultasSelect.options.length; i++) {
                        if (fakultasSelect.options[i].value == fakultasId) {
                            fakultasSelect.options[i].selected = true;
                            break;
                        }
                    }

                    // Tampilkan form edit
                    openForm('editForm');
                }

                // Fungsi untuk membuka dan menutup form
                function openForm(formId) {
                    document.getElementById(formId).style.display = 'block';
                    document.getElementById('overlay').style.display = 'block';
                }

                function closeForm(formId) {
                    document.getElementById(formId).style.display = 'none';
                    document.getElementById('overlay').style.display = 'none';
                }

                // Fungsi untuk menghapus jurusan dengan konfirmasi
                function hapusJurusan(id) {
                    if (confirm('Apakah Anda yakin ingin menghapus jurusan ini?')) {
                        // Redirect ke URL dengan parameter hapus_jurusan
                        window.location.href = '?hapus_jurusan=' + id;
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
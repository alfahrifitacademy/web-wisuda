<?php
session_start();
// Periksa apakah admin sudah login
if (!isset($_SESSION['admin'])) {
    header("Location: login.php"); // Redirect ke login jika belum login
    exit;
}

// Hubungkan ke database
include '../admin/db_connnection.php';

// Ambil data users dengan nama fakultas dan jurusan menggunakan LEFT JOIN
$users = mysqli_query($koneksi, "
    SELECT u.id_users, u.nama, u.nim, IFNULL(f.fakultas, 'Tidak Tersedia') AS fakultas, IFNULL(j.jurusan, 'Tidak Tersedia') AS jurusan
    FROM users u
    LEFT JOIN fakultas f ON u.fakultas = f.id_fakultas
    LEFT JOIN jurusan j ON u.jurusan = j.id_jurusan
");

// Ambil data fakultas untuk dropdown
$fakultas_data = mysqli_query($koneksi, "SELECT * FROM fakultas");

// Ambil data jurusan untuk dropdown
$jurusan_data = mysqli_query($koneksi, "SELECT * FROM jurusan");

// Ambil data pencarian dari input pengguna
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Query untuk mendapatkan data mahasiswa dengan filter pencarian
$sql = "SELECT u.id_users, u.nama, u.nim, IFNULL(f.fakultas, 'Tidak Tersedia') AS fakultas, IFNULL(j.jurusan, 'Tidak Tersedia') AS jurusan
        FROM users u
        LEFT JOIN fakultas f ON u.fakultas = f.id_fakultas
        LEFT JOIN jurusan j ON u.jurusan = j.id_jurusan
        WHERE u.nama LIKE '%$search%'
        OR u.nim LIKE '%$search%'
        OR f.fakultas LIKE '%$search%'
        OR j.jurusan LIKE '%$search%'
        ORDER BY u.id_users ASC";
$users = mysqli_query($koneksi, $sql);

// Tambah data user
if (isset($_POST['tambah_user'])) {
    $nama = $_POST['nama'];
    $nim = $_POST['nim'];
    $fakultas = $_POST['fakultas'];
    $jurusan = $_POST['jurusan'];

    $query = "INSERT INTO users (nama, nim, fakultas, jurusan) VALUES ('$nama', '$nim', '$fakultas', '$jurusan')";
    mysqli_query($koneksi, $query);
    header('Location: data_mahasiswa.php');
}

// Update data user
if (isset($_POST['update_user'])) {
    $id_users = $_POST['id_users'];
    $nama = $_POST['nama'];
    $nim = $_POST['nim'];
    $fakultas = $_POST['fakultas'];
    $jurusan = $_POST['jurusan'];

    $query = "UPDATE users SET nama='$nama', nim='$nim', fakultas='$fakultas', jurusan='$jurusan' WHERE id_users='$id_users'";
    mysqli_query($koneksi, $query);
    header('Location: data_mahasiswa.php');
}

// Hapus data pengguna dan data terkait
if (isset($_GET['hapus_user'])) {
    $id_users = $_GET['hapus_user'];

    // Hapus data terkait di tabel dokumen dan guest
    $query_dokumen = "DELETE FROM dokumen WHERE create_by='$id_users'";
    $query_guest = "DELETE FROM guest WHERE create_by='$id_users'";
    mysqli_query($koneksi, $query_dokumen);
    mysqli_query($koneksi, $query_guest);

    // Hapus data pengguna di tabel users
    $query_users = "DELETE FROM users WHERE id_users='$id_users'";
    if (mysqli_query($koneksi, $query_users)) {
        echo "<script>alert('Pengguna dan data terkait berhasil dihapus');</script>";
    } else {
        echo "<script>alert('Gagal menghapus pengguna');</script>";
    }

    header('Location: data_mahasiswa.php');
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../admin/assets/css/DashboardAdmin.css">
</head>

<body>
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

        <div class="main">
            <div class="topbar">
                <div class="toggle"><ion-icon name="menu-outline"></ion-icon></div>
            </div>
            <div class="containerTable">
                <h2>Tabel Mahasiswa</h2>
                <!-- Form Pencarian -->
                <form method="GET" action="data_mahasiswa.php" style="margin-bottom: 20px;">
                    <input type="text" name="search" id="searchInput" placeholder="Cari nama, nim, fakultas, atau jurusan..." value="<?= htmlspecialchars($search); ?>">
                    <button type="submit">Cari</button>
                </form>
                <button id="tambahMahasiswa" onclick="openForm('tambahForm')">Tambah Mahasiswa</button>

                <table id="mahasiswaTable" class="table-mahasiswa">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>NIM</th>
                            <th>Fakultas</th>
                            <th>Jurusan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1;
                        while ($user = mysqli_fetch_assoc($users)) { ?>
                            <tr>
                                <td><?= $i++; ?></td>
                                <td><?= $user['nama']; ?></td>
                                <td><?= $user['nim']; ?></td>
                                <td><?= $user['fakultas']; ?></td>
                                <td><?= $user['jurusan']; ?></td>
                                <td class="aksi">
                                    <button class="btn-edit" onclick="editUser(<?= $user['id_users']; ?>, '<?= addslashes($user['nama']); ?>', '<?= $user['nim']; ?>', '<?= $user['fakultas']; ?>', '<?= $user['jurusan']; ?>')">Edit</button>
                                    <button class="btn-hapus" onclick="hapusUser(<?= $user['id_users']; ?>)">Hapus</button>
                                </td>

                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <!-- Form Tambah Mahasiswa -->
                <div id="tambahForm" class="form-modal">
                    <form method="POST">
                        <h3>Tambah Mahasiswa</h3>
                        <input type="text" name="nama" placeholder="Nama" required>
                        <input type="text" name="nim" placeholder="NIM" required>

                        <select name="fakultas" id="fakultasSelect" onchange="filterJurusan('jurusanSelect')" required>
                            <option value="">Pilih Fakultas</option>
                            <?php while ($fakultas = mysqli_fetch_assoc($fakultas_data)) { ?>
                                <option value="<?= $fakultas['id_fakultas']; ?>"><?= $fakultas['fakultas']; ?></option>
                            <?php } ?>
                        </select>

                        <select name="jurusan" id="jurusanSelect" required>
                            <option value="">Pilih Jurusan</option>
                            <?php while ($jurusan = mysqli_fetch_assoc($jurusan_data)) { ?>
                                <option value="<?= $jurusan['id_jurusan']; ?>" data-fakultas="<?= $jurusan['fakultas_id']; ?>"><?= $jurusan['jurusan']; ?></option>
                            <?php } ?>
                        </select>

                        <!-- Tombol tambah tetap berukuran penuh -->
                        <button type="submit" name="tambah_user">Tambah</button>
                    </form>
                </div>

                <!-- Form Edit Mahasiswa -->
                <div id="editForm" class="form-modal">
                    <form method="POST">
                        <h3>Edit Mahasiswa</h3>
                        <input type="hidden" name="id_users" id="editId">
                        <input type="text" name="nama" id="editNama" placeholder="Nama" required>
                        <input type="text" name="nim" id="editNIM" placeholder="NIM" required>

                        <select name="fakultas" id="editFakultasSelect" onchange="filterJurusan('editJurusanSelect')" required>
                            <option value="">-- Pilih Fakultas --</option>
                            <?php
                            mysqli_data_seek($fakultas_data, 0);
                            while ($fakultas = mysqli_fetch_assoc($fakultas_data)) { ?>
                                <option value="<?= $fakultas['id_fakultas']; ?>"><?= $fakultas['fakultas']; ?></option>
                            <?php } ?>
                        </select>

                        <select name="jurusan" id="editJurusanSelect" required>
                            <option value="">-- Pilih Jurusan --</option>
                            <?php
                            mysqli_data_seek($jurusan_data, 0);
                            while ($jurusan = mysqli_fetch_assoc($jurusan_data)) { ?>
                                <option value="<?= $jurusan['id_jurusan']; ?>" data-fakultas="<?= $jurusan['fakultas_id']; ?>"><?= $jurusan['jurusan']; ?></option>
                            <?php } ?>
                        </select>

                        <!-- Tombol update tetap berukuran penuh -->
                        <button type="submit" name="update_user">Update</button>
                    </form>
                </div>



                <!-- Overlay -->
                <div id="overlay" onclick="closeOverlay()" style="display:none;"></div>

                <script>
                    // Fungsi yang sama seperti sebelumnya
                    function closeForm(formId) {
                        document.getElementById(formId).style.display = 'none';
                        document.getElementById('overlay').style.display = 'none';
                    }

                    function openForm(formId) {
                        document.getElementById(formId).style.display = 'block';
                        document.getElementById('overlay').style.display = 'block';
                    }

                    function closeOverlay() {
                        document.getElementById('tambahForm').style.display = 'none';
                        document.getElementById('editForm').style.display = 'none';
                        document.getElementById('overlay').style.display = 'none';
                    }

                    // Filter jurusan berdasarkan fakultas yang dipilih
                    function filterJurusan(jurusanSelectId) {
                        var fakultasId = document.getElementById(jurusanSelectId == 'jurusanSelect' ? 'fakultasSelect' : 'editFakultasSelect').value;
                        var jurusanOptions = document.getElementById(jurusanSelectId).options;

                        for (var i = 0; i < jurusanOptions.length; i++) {
                            var option = jurusanOptions[i];
                            option.style.display = option.getAttribute('data-fakultas') == fakultasId ? 'block' : 'none';
                        }
                        document.getElementById(jurusanSelectId).value = ""; // Reset nilai jurusan setelah difilter
                    }

                    // Edit user
                    function editUser(id, nama, nim, fakultas, jurusan) {
                        document.getElementById('editId').value = id;
                        document.getElementById('editNama').value = nama;
                        document.getElementById('editNIM').value = nim;
                        document.getElementById('editFakultasSelect').value = fakultas;
                        filterJurusan('editJurusanSelect');
                        document.getElementById('editJurusanSelect').value = jurusan;
                        openForm('editForm');
                    }

                    // Fungsi untuk menghapus pengguna dengan konfirmasi
                    function hapusUser(id) {
                        if (confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) {
                            // Redirect ke URL dengan parameter hapus_user
                            window.location.href = '?hapus_user=' + id;
                        }
                    }

                    function searchTable() {
                        var input = document.getElementById('searchInput');
                        var filter = input.value.toLowerCase();
                        var table = document.getElementById('userTable');
                        var rows = table.getElementsByTagName('tr');

                        for (var i = 1; i < rows.length; i++) {
                            var cells = rows[i].getElementsByTagName('td');
                            var match = false;

                            for (var j = 0; j < cells.length; j++) {
                                if (cells[j]) {
                                    var cellValue = cells[j].textContent || cells[j].innerText;
                                    if (cellValue.toLowerCase().indexOf(filter) > -1) {
                                        match = true;
                                        break;
                                    }
                                }
                            }

                            rows[i].style.display = match ? '' : 'none';
                        }
                    }

                    // Fungsi untuk hapus pengguna dengan konfirmasi
                    function hapusUser(id) {
                        if (confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) {
                            window.location.href = '?hapus_user=' + id;
                        }
                    }
                </script>
            </div>
        </div>

        <script src="../admin/assets/js/main.js"></script>
        <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
        <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>
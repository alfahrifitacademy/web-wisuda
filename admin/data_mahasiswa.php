<?php
session_start();
include '../admin/db_connnection.php'; // Pastikan file ini terhubung ke database

// Periksa apakah admin sudah login
if (!isset($_SESSION['admin'])) {
    header("Location: login.php"); // Redirect ke login jika belum login
    exit;
}

// Handle Create (Tambah User)
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $npm = $_POST['npm'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $fakultas = $_POST['fakultas'];
    $jurusan = $_POST['jurusan'];
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    $query = "INSERT INTO users (nama, npm, password, fakultas, jurusan, is_admin, created_at) 
              VALUES ('$nama', '$npm', '$password', '$fakultas', '$jurusan', '$is_admin', NOW())";
    mysqli_query($koneksi, $query);
    header('Location: data_mahasiswa.php');
}

// Handle Update (Edit User)
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $npm = $_POST['npm'];
    $fakultas = $_POST['fakultas'];
    $jurusan = $_POST['jurusan'];
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    $query = "UPDATE users SET nama='$nama', npm='$npm', fakultas='$fakultas', jurusan='$jurusan', is_admin='$is_admin'
              WHERE id_users='$id'";
    mysqli_query($koneksi, $query);
    header('Location: data_mahasiswa.php');
}

// Handle Delete (Hapus User)
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM users WHERE id_users='$id'";
    mysqli_query($koneksi, $query);
    header('Location: data_mahasiswa.php');
}

// Ambil semua data pengguna
$result = mysqli_query($koneksi, "SELECT * FROM users");
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
                    <a href="../admin/pengunguman.php">
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

            <h2>Data Mahasiswa</h2>
            <button onclick="document.getElementById('tambahModal').style.display='block'">Tambah User</button>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>NPM</th>
                        <th>Fakultas</th>
                        <th>Jurusan</th>
                        <th>Admin</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?= $row['id_users']; ?></td>
                            <td><?= $row['nama']; ?></td>
                            <td><?= $row['npm']; ?></td>
                            <td><?= $row['fakultas']; ?></td>
                            <td><?= $row['jurusan']; ?></td>
                            <td><?= $row['is_admin'] ? 'Ya' : 'Tidak'; ?></td>
                            <td>
                                <button onclick="editUser(<?= $row['id_users']; ?>)">Edit</button>
                                <a href="data_mahasiswa.php?delete=<?= $row['id_users']; ?>" onclick="return confirm('Yakin hapus?')">Hapus</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

            <div id="tambahModal" style="display:none;">
                <form method="POST">
                    <input type="text" name="nama" placeholder="Nama" required>
                    <input type="text" name="npm" placeholder="NPM" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <input type="text" name="fakultas" placeholder="Fakultas" required>
                    <input type="text" name="jurusan" placeholder="Jurusan" required>
                    <label><input type="checkbox" name="is_admin"> Admin</label>
                    <button type="submit" name="tambah">Tambah</button>
                </form>
            </div>

            <script>
                function editUser(id) {
                    // Logika edit bisa menggunakan modal atau halaman terpisah
                    alert('Edit User ID: ' + id);
                }
            </script>
        </div>
    </div>

    <script src="../admin/assets/js/main.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>

</html>
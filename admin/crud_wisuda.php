<?php
include '../admin/db_connnection.php';

// Handle Tambah Data
if (isset($_POST['add'])) {
    $nama = $_POST['nama'];
    $npm = $_POST['npm'];
    $fakultas = $_POST['fakultas'];
    $jurusan = $_POST['jurusan'];
    $is_admin = $_POST['is_admin'];

    $sql = "INSERT INTO users (nama, npm, fakultas, jurusan, is_admin) 
            VALUES ('$nama', '$npm', '$fakultas', '$jurusan', $is_admin)";
    $koneksi->query($sql);
    header('Location: ../admin/data_wisuda.php');
}

// Handle Edit Data
if (isset($_POST['edit'])) {
    $id = $_POST['id_users'];
    $nama = $_POST['nama'];
    $npm = $_POST['npm'];
    $fakultas = $_POST['fakultas'];
    $jurusan = $_POST['jurusan'];

    $sql = "UPDATE users SET nama='$nama', npm='$npm', fakultas='$fakultas', jurusan='$jurusan' 
            WHERE id_users=$id";
    $koneksi->query($sql);
    header('Location: ../admin/data_wisuda.php');
}

// Handle Hapus Data
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM users WHERE id_users=$id";
    $koneksi->query($sql);
    header('Location: ../admin/data_wisuda.php');
}

// Fetch Data
$result = $koneksi->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- ======= Styles ====== -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../admin/assets/css/DashboardAdmin.css">
    <title>Data Mahasiswa</title>
</head>
<body>
    <h2>Data Mahasiswa</h2>

    <!-- Form Tambah/Edit Data -->
    <form method="POST" action="../admin/data_wisuda.php">
        <input type="hidden" name="id_users" value="<?php echo isset($_GET['edit']) ? $_GET['edit'] : ''; ?>">
        
        <label>Nama:</label><br>
        <input type="text" name="nama" value="<?php echo isset($row['nama']) ? $row['nama'] : ''; ?>" required><br><br>

        <label>NPM:</label><br>
        <input type="text" name="npm" value="<?php echo isset($row['npm']) ? $row['npm'] : ''; ?>" required><br><br>

        <label>Fakultas:</label><br>
        <input type="text" name="fakultas" value="<?php echo isset($row['fakultas']) ? $row['fakultas'] : ''; ?>"><br><br>

        <label>Jurusan:</label><br>
        <input type="text" name="jurusan" value="<?php echo isset($row['jurusan']) ? $row['jurusan'] : ''; ?>"><br><br>

        <label>Status:</label><br>
        <select name="is_admin">
            <option value="0" <?php echo isset($row['is_admin']) && !$row['is_admin'] ? 'selected' : ''; ?>>Mahasiswa</option>
            <option value="1" <?php echo isset($row['is_admin']) && $row['is_admin'] ? 'selected' : ''; ?>>Admin</option>
        </select><br><br>

        <button type="submit" name="<?php echo isset($_GET['edit']) ? 'edit' : 'add'; ?>">
            <?php echo isset($_GET['edit']) ? 'Update Data' : 'Tambah Data'; ?>
        </button>
    </form>

    <br><hr><br>

    <!-- Tabel Data Mahasiswa -->
    <table border="1">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>NPM</th>
                <th>Fakultas</th>
                <th>Jurusan</th>
                <th>Status</th>
                <th>Dibuat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>" . $no++ . "</td>
                    <td>" . htmlspecialchars($row['nama']) . "</td>
                    <td>" . htmlspecialchars($row['npm']) . "</td>
                    <td>" . htmlspecialchars($row['fakultas']) . "</td>
                    <td>" . htmlspecialchars($row['jurusan']) . "</td>
                    <td>" . ($row['is_admin'] ? 'Admin' : 'Mahasiswa') . "</td>
                    <td>" . htmlspecialchars($row['created_at']) . "</td>
                    <td>
                        <a href='../admin/edit.php?edit=" . $row['id_users'] . "'>Edit</a> |
                        <a href='#?delete=" . $row['id_users'] . "' onclick='return confirm(\"Hapus data ini?\");'>Hapus</a>
                    </td>
                </tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>

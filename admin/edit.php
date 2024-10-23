<?php
include '../admin/db_connnection.php'; // Masukkan file koneksi database

// Mendapatkan ID user dari parameter URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Cek apakah form telah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil data yang diinput dari form
    $nama = htmlspecialchars($_POST['nama']);
    $npm = htmlspecialchars($_POST['npm']);
    $fakultas = htmlspecialchars($_POST['fakultas']);
    $jurusan = htmlspecialchars($_POST['jurusan']);
    $status = isset($_POST['status']) ? intval($_POST['status']) : 0;

    // Query update untuk mengubah data mahasiswa berdasarkan ID
    $sql = "UPDATE users SET nama=?, npm=?, fakultas=?, jurusan=?, is_admin=? WHERE id_users=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssii", $nama, $npm, $fakultas, $jurusan, $status, $id);

    if ($stmt->execute()) {
        // Redirect ke halaman daftar mahasiswa setelah update berhasil
        header('Location: ../admin/dashboard.php');
        exit();
    } else {
        echo "Terjadi kesalahan saat mengupdate data: " . $conn->error;
    }
}

// Mendapatkan data mahasiswa berdasarkan ID untuk ditampilkan di form edit
$sql = "SELECT * FROM users WHERE id_users = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "Data tidak ditemukan!";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Mahasiswa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Edit Data Mahasiswa</h2>
    <form action="edit.php?id=<?php echo $id; ?>" method="POST">
        <label for="nama">Nama:</label>
        <input type="text" name="nama" id="nama" value="<?php echo htmlspecialchars($row['nama']); ?>" required>
        
        <label for="npm">NIM:</label>
        <input type="text" name="npm" id="npm" value="<?php echo htmlspecialchars($row['npm']); ?>" required>
        
        <label for="fakultas">Fakultas:</label>
        <input type="text" name="fakultas" id="fakultas" value="<?php echo htmlspecialchars($row['fakultas']); ?>" required>
        
        <label for="jurusan">Jurusan:</label>
        <input type="text" name="jurusan" id="jurusan" value="<?php echo htmlspecialchars($row['jurusan']); ?>" required>

        <label for="status">Status:</label>
        <select name="status" id="status">
            <option value="0" <?php echo $row['is_admin'] == 0 ? 'selected' : ''; ?>>Mahasiswa</option>
            <option value="1" <?php echo $row['is_admin'] == 1 ? 'selected' : ''; ?>>Admin</option>
        </select>

        <button type="submit" class="btn">Simpan Perubahan</button>
        <a href="../admin/dashboard.php" class="btn-cancel">Batal</a>
    </form>
</div>

</body>
</html>

<?php
// Menutup koneksi database
$conn->close();
?>

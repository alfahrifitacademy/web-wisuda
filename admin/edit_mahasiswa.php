<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "nama_database");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $nim = $_POST['nim'];
    $jurusan = $_POST['jurusan'];

    // Query untuk update data mahasiswa
    $sql = "UPDATE mahasiswa SET nama='$nama', nim='$nim', jurusan='$jurusan' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "Data berhasil diupdate!";
        header("Location: DashboardAdmin.php"); // Redirect ke halaman utama
    } else {
        echo "Error updating record: " . $conn->error;
    }
} else {
    $id = $_GET['id'];
    $sql = "SELECT * FROM mahasiswa WHERE id=$id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Mahasiswa</title>
</head>
<body>
    <h2>Edit Data Mahasiswa</h2>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
        Nama: <input type="text" name="nama" value="<?php echo $row['nama']; ?>" required><br/><br/>
        NIM: <input type="text" name="nim" value="<?php echo $row['nim']; ?>" required><br/><br/>
        Jurusan: <input type="text" name="jurusan" value="<?php echo $row['jurusan']; ?>" required><br/><br/>
        <input type="submit" value="Update">
    </form>
</body>
</html>

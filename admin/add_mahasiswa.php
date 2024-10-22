<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $nim = $_POST['nim'];
    $jurusan = $_POST['jurusan'];

    // Koneksi ke database
    $conn = new mysqli("localhost", "root", "", "nama_database");

    // Cek koneksi
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    // Query untuk menambah data mahasiswa
    $sql = "INSERT INTO mahasiswa (nama, nim, jurusan) VALUES ('$nama', '$nim', '$jurusan')";

    if ($conn->query($sql) === TRUE) {
        echo "Data berhasil ditambahkan!";
        header("Location: DashboardAdmin.php"); // Redirect ke halaman utama
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Mahasiswa</title>
</head>
<body>
    <h2>Tambah Data Mahasiswa</h2>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        Nama: <input type="text" name="nama" required><br/><br/>
        NIM: <input type="text" name="nim" required><br/><br/>
        Jurusan: <input type="text" name="jurusan" required><br/><br/>
        <input type="submit" value="Tambah">
    </form>
</body>
</html>

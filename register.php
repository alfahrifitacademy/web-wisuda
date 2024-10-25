<?php
// Koneksi ke database
include 'admin/db_connnection.php';

// Query untuk mendapatkan semua fakultas
$queryFakultas = "SELECT * FROM fakultas";
$resultFakultas = $koneksi->query($queryFakultas);

// Query untuk mendapatkan semua jurusan
$queryJurusan = "SELECT * FROM jurusan";
$resultJurusan = $koneksi->query($queryJurusan);

// Siapkan array untuk menyimpan data jurusan
$jurusanList = [];
while ($rowJurusan = $resultJurusan->fetch_assoc()) {
    $jurusanList[] = $rowJurusan;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Registrasi</title>
    <link rel="stylesheet" href="assets/css/style_regist.css">
</head>
<body>

<div class="form-container">
    <h2>Silahkan Daftar</h2>
    <p>Pastikan Setiap Form Diisi Dengan Benar!</p>

    <form action="proses_register.php" method="POST">
        <input type="text" name="nama" placeholder="Nama" required>
        <input type="text" name="npm" placeholder="NPM" required>

        <!-- Dropdown Fakultas -->
        <select name="fakultas" id="fakultas" required>
            <option value="">--Pilih Fakultas--</option>
            <?php while ($rowFakultas = $resultFakultas->fetch_assoc()) { ?>
                <option value="<?php echo $rowFakultas['id_fakultas']; ?>">
                    <?php echo $rowFakultas['fakultas']; ?>
                </option>
            <?php } ?>
        </select>

        <!-- Dropdown Jurusan -->
        <select name="jurusan" id="jurusan" required>
            <option value="">--Pilih Jurusan--</option>
        </select>

        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Ulangi Password" required>

        <button type="submit">Daftar</button>
        <a href="login.php">Sudah Memiliki Akun? Masuk</a>
    </form>
</div>

<!-- Script untuk Mengatur Dropdown Jurusan -->
<script>
    const jurusanList = <?php echo json_encode($jurusanList); ?>;

    document.getElementById('fakultas').addEventListener('change', function () {
        const fakultasId = this.value;
        const jurusanSelect = document.getElementById('jurusan');

        // Kosongkan jurusan setiap kali fakultas berubah
        jurusanSelect.innerHTML = '<option value="">--Pilih Jurusan--</option>';

        // Tambahkan jurusan yang sesuai dengan fakultas yang dipilih
        jurusanList.forEach(function (jurusan) {
            if (jurusan.fakultas_id == fakultasId) {
                const option = document.createElement('option');
                option.value = jurusan.id_jurusan;
                option.textContent = jurusan.jurusan;
                jurusanSelect.appendChild(option);
            }
        });
    });
</script>

</body>
</html>

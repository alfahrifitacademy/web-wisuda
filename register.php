<?php
// Mulai session untuk mengambil pesan alert
session_start();

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

// Mengambil pesan error atau success dari session
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';

// Menghapus pesan dari session setelah ditampilkan
unset($_SESSION['error_message']);
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

<div class="alert-container">
    <!-- Tampilkan pesan jika ada -->
    <?php if ($error_message): ?>
        <div class="alert error">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>
</div>

<div class="form-container">
    <h2>Silahkan Daftar</h2>
    <p>Pastikan Setiap Form Diisi Dengan Benar!</p>

    <form action="proses_register.php" method="POST">
        <input type="text" name="nama" placeholder="Nama" required>
        <input type="text" name="nim" id="nim" placeholder="NIM" required minlength="7" maxlength="7" pattern="\d+" title="NIM harus terdiri dari 7 digit angka">

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
    // Menangani submit form untuk validasi NIM
    document.querySelector('form').addEventListener('submit', function(event) {
        var nimInput = document.getElementById('nim');
        var nimValue = nimInput.value;

        // Validasi NIM, hanya angka dan panjangnya harus tepat 7 digit
        if (nimValue.length !== 7 || !/^\d+$/.test(nimValue)) {
            event.preventDefault(); // Mencegah form untuk submit
            alert('NIM harus terdiri dari 7 digit angka.');
            nimInput.focus(); // Fokuskan ke input NIM
        }
    });
</script>

</body>
</html>

<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $nim = $_POST['nim'];
    $jurusan = $_POST['jurusan'];
    $fakultas = $_POST['fakultas'];

    // Validasi file
    $allowed_types = ['jpg', 'jpeg', 'png', 'pdf'];
    $max_file_size = 5 * 1024 * 1024; // 5MB

    // Akte Kelahiran
    $akte_kelahiran = $_FILES['akte_kelahiran'];
    $akte_extension = strtolower(pathinfo($akte_kelahiran['name'], PATHINFO_EXTENSION));
    
    // Ijazah
    $ijazah = $_FILES['ijazah'];
    $ijazah_extension = strtolower(pathinfo($ijazah['name'], PATHINFO_EXTENSION));

    // Bukti Pembayaran
    $bukti_pembayaran = $_FILES['bukti_pembayaran'];
    $bukti_extension = strtolower(pathinfo($bukti_pembayaran['name'], PATHINFO_EXTENSION));

    // Validasi ukuran file dan tipe file
    if ($akte_kelahiran['size'] > $max_file_size || !in_array($akte_extension, $allowed_types)) {
        die("Akte kelahiran harus berformat JPG, JPEG, PNG, PDF dan ukurannya tidak boleh lebih dari 5MB.");
    }

    if ($ijazah['size'] > $max_file_size || !in_array($ijazah_extension, $allowed_types)) {
        die("Ijazah harus berformat JPG, JPEG, PNG, PDF dan ukurannya tidak boleh lebih dari 5MB.");
    }

    if ($bukti_pembayaran['size'] > $max_file_size || !in_array($bukti_extension, $allowed_types)) {
        die("Bukti pembayaran harus berformat JPG, JPEG, PNG, PDF dan ukurannya tidak boleh lebih dari 5MB.");
    }

    // Simpan file yang di-upload ke folder tertentu
    $upload_dir = 'uploads/';
    move_uploaded_file($akte_kelahiran['tmp_name'], $upload_dir . $nim . '_akte.' . $akte_extension);
    move_uploaded_file($ijazah['tmp_name'], $upload_dir . $nim . '_ijazah.' . $ijazah_extension);
    move_uploaded_file($bukti_pembayaran['tmp_name'], $upload_dir . $nim . '_bukti.' . $bukti_extension);

    // Simpan data ke database (contoh)
    $conn = new mysqli('localhost', 'root', '', 'undangan_wisuda');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO pendaftaran_wisuda (nama, nim, jurusan, fakultas, akte_kelahiran, ijazah, bukti_pembayaran)
            VALUES ('$nama', '$nim', '$jurusan', '$fakultas', '" . $nim . "_akte.$akte_extension', '" . $nim . "_ijazah.$ijazah_extension', '" . $nim . "_bukti.$bukti_extension')";

    if ($conn->query($sql) === TRUE) {
        echo "Pendaftaran berhasil!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

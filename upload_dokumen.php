<?php
session_start();
require 'admin/db_connnection.php'; // Mengimpor koneksi database

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Maksimal ukuran file (5MB)
$maxFileSize = 5 * 1024 * 1024;

// Cek apakah form telah di-submit
if (isset($_POST['submit'])) {
    // Direktori penyimpanan file
    $targetDir = __DIR__ . "/uploads/"; // Gunakan path absolut

    // Pastikan folder uploads ada
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true); // Buat folder jika belum ada
    }

    // Ambil informasi file
    $fileAkte = $_FILES["file_akte"];
    $fileIjasa = $_FILES["file_ijasa"];
    $filePembayaran = $_FILES["file_pembayaran"];

    // Validasi apakah file ada
    if ($fileAkte['error'] != 0 || $fileIjasa['error'] != 0 || $filePembayaran['error'] != 0) {
        $_SESSION['upload_error'] = "Gagal mengunggah, periksa file yang dipilih.";
        header("Location: daftar_wisuda.php");
        exit();
    }

    // Validasi jenis file akte (hanya PDF)
    $fileAkteExt = strtolower(pathinfo($fileAkte["name"], PATHINFO_EXTENSION));
    if ($fileAkteExt !== 'pdf') {
        $_SESSION['upload_error'] = "File akte harus dalam format PDF.";
        header("Location: daftar_wisuda.php");
        exit();
    }

    // Validasi jenis file ijazah (hanya PDF)
    $fileIjasaExt = strtolower(pathinfo($fileIjasa["name"], PATHINFO_EXTENSION));
    if ($fileIjasaExt !== 'pdf') {
        $_SESSION['upload_error'] = "File ijazah harus dalam format PDF.";
        header("Location: daftar_wisuda.php");
        exit();
    }

    // Validasi jenis file pembayaran (hanya PNG atau JPG)
    $filePembayaranExt = strtolower(pathinfo($filePembayaran["name"], PATHINFO_EXTENSION));
    if (!in_array($filePembayaranExt, ['png', 'jpg', 'jpeg'])) {
        $_SESSION['upload_error'] = "File pembayaran harus dalam format PNG atau JPG.";
        header("Location: daftar_wisuda.php");
        exit();
    }

    // Validasi ukuran file
    if ($fileAkte['size'] > $maxFileSize || $fileIjasa['size'] > $maxFileSize || $filePembayaran['size'] > $maxFileSize) {
        $_SESSION['upload_error'] = "Ukuran file tidak boleh lebih dari 5MB.";
        header("Location: daftar_wisuda.php");
        exit();
    }

    // Proses upload file akte
    $fileAkteName = uniqid() . "_" . basename($fileAkte["name"]);
    $targetFilePathAkte = $targetDir . $fileAkteName;
    if (!move_uploaded_file($fileAkte["tmp_name"], $targetFilePathAkte)) {
        $_SESSION['upload_error'] = "Gagal mengunggah file akte.";
        header("Location: daftar_wisuda.php");
        exit();
    }

    // Proses upload file ijazah
    $fileIjasaName = uniqid() . "_" . basename($fileIjasa["name"]);
    $targetFilePathIjasa = $targetDir . $fileIjasaName;
    if (!move_uploaded_file($fileIjasa["tmp_name"], $targetFilePathIjasa)) {
        $_SESSION['upload_error'] = "Gagal mengunggah file ijazah.";
        header("Location: daftar_wisuda.php");
        exit();
    }

    // Proses upload file pembayaran
    $filePembayaranName = uniqid() . "_" . basename($filePembayaran["name"]);
    $targetFilePathPembayaran = $targetDir . $filePembayaranName;
    if (!move_uploaded_file($filePembayaran["tmp_name"], $targetFilePathPembayaran)) {
        $_SESSION['upload_error'] = "Gagal mengunggah file pembayaran.";
        header("Location: daftar_wisuda.php");
        exit();
    }

    // Menyimpan path file ke database
    $userId = $_SESSION['user_id']; // ID pengguna yang login
    $sql = "INSERT INTO dokumen (file_akte, file_ijasa, file_pembayaran, create_by, status) VALUES (?, ?, ?, ?, 'pending')";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("sssi", $fileAkteName, $fileIjasaName, $filePembayaranName, $userId);

    if ($stmt->execute()) {
        // Menandai pendaftaran wisuda berhasil
        $_SESSION['daftar_wisuda'] = true; // Menandakan user sudah mendaftar
        $_SESSION['upload_success'] = "Dokumen berhasil diunggah. Status saat ini: pending.";
    } else {
        $_SESSION['upload_error'] = "Gagal menyimpan dokumen, silahkan coba lagi.";
    }

    // Set status dokumen menjadi 'pending'
    $_SESSION['status_dokumen'] = 'pending';

    // Simulasi setelah dokumen di-upload, status berubah menjadi 'approved' atau 'rejected'
    // Anda bisa mengganti ini sesuai dengan proses pemeriksaan dokumen

    $_SESSION['status_dokumen'] = 'approved'; // Ubah ini jika dokumen disetujui
    $_SESSION['daftar_wisuda'] = true; // Tandai pengguna sudah mendaftar

    $stmt->close();
    $koneksi->close();

    header("Location: dashboard.php");
    exit();
}
?>

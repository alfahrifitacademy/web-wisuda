<?php
session_start();
require 'admin/db_connnection.php'; // Mengimpor koneksi database

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Cek apakah form telah di-submit
if (isset($_POST['submit'])) {
    // Direktori penyimpanan file
    $targetDir = "uploads/";

    // Ambil ekstensi file
    $fileAkteExt = strtolower(pathinfo($_FILES["file_akte"]["name"], PATHINFO_EXTENSION));
    $fileIjasaExt = strtolower(pathinfo($_FILES["file_ijasa"]["name"], PATHINFO_EXTENSION));
    $filePembayaranExt = strtolower(pathinfo($_FILES["file_pembayaran"]["name"], PATHINFO_EXTENSION));

    // Validasi jenis file akte (hanya PDF)
    if ($fileAkteExt !== 'pdf') {
        echo "File akte harus dalam format PDF.";
        exit();
    }

    // Validasi jenis file ijazah (hanya PDF)
    if ($fileIjasaExt !== 'pdf') {
        echo "File ijazah harus dalam format PDF.";
        exit();
    }

    // Validasi jenis file pembayaran (hanya PNG atau JPG)
    if (!in_array($filePembayaranExt, ['png', 'jpg', 'jpeg'])) {
        echo "File pembayaran harus dalam format PNG atau JPG.";
        exit();
    }

    // Proses upload file akte
    $fileAkteName = basename($_FILES["file_akte"]["name"]);
    $targetFilePathAkte = $targetDir . uniqid() . "_" . $fileAkteName; // Buat nama unik untuk file
    if (move_uploaded_file($_FILES["file_akte"]["tmp_name"], $targetFilePathAkte)) {
        $fileAktePath = $targetFilePathAkte;
    } else {
        echo "Gagal mengunggah file akte.";
        exit();
    }

    // Proses upload file ijazah
    $fileIjasaName = basename($_FILES["file_ijasa"]["name"]);
    $targetFilePathIjasa = $targetDir . uniqid() . "_" . $fileIjasaName;
    if (move_uploaded_file($_FILES["file_ijasa"]["tmp_name"], $targetFilePathIjasa)) {
        $fileIjasaPath = $targetFilePathIjasa;
    } else {
        echo "Gagal mengunggah file ijazah.";
        exit();
    }

    // Proses upload file pembayaran
    $filePembayaranName = basename($_FILES["file_pembayaran"]["name"]);
    $targetFilePathPembayaran = $targetDir . uniqid() . "_" . $filePembayaranName;
    if (move_uploaded_file($_FILES["file_pembayaran"]["tmp_name"], $targetFilePathPembayaran)) {
        $filePembayaranPath = $targetFilePathPembayaran;
    } else {
        echo "Gagal mengunggah file pembayaran.";
        exit();
    }

    // Menyimpan path file ke database
    $userId = $_SESSION['user_id']; // ID pengguna yang login
    $sql = "INSERT INTO dokumen (file_akte, file_ijasa, file_pembayaran, create_by) VALUES (?, ?, ?, ?)";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("sssi", $fileAktePath, $fileIjasaPath, $filePembayaranPath, $userId);

    if ($stmt->execute()) {
        $_SESSION['upload_success'] = "Untuk proses selanjutnya silahkan tunggu dan cek di bagian kartu undangan.";
    } else {
        $_SESSION['upload_success'] = "Gagal menyimpan dokumen, silahkan coba lagi.";
    }

    $stmt->close();
    $koneksi->close();

    header("Location: daftar_wisuda.php");
    exit();
}
?>

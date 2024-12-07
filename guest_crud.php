<?php
session_start();
require 'admin/db_connnection.php'; // Mengimpor koneksi database

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Cek action yang dikirim melalui URL
$action = isset($_GET['action']) ? $_GET['action'] : 'read';
$user_id = $_SESSION['user_id'];

switch ($action) {
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $kepada = $_POST['kepada'];
            $bukti_pembayaran = $_FILES['bukti_pembayaran'];

            // Proses upload file
            if ($bukti_pembayaran['error'] === 0) {
                $file_name = $bukti_pembayaran['name'];
                $file_tmp = $bukti_pembayaran['tmp_name'];
                $file_size = $bukti_pembayaran['size'];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                // Validasi ekstensi dan ukuran file
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];
                $max_size = 5 * 1024 * 1024; // Maksimum 5 MB

                if (in_array($file_ext, $allowed_extensions) && $file_size <= $max_size) {
                    // Tentukan nama file yang unik
                    $new_file_name = time() . '.' . $file_ext;

                    // Pindahkan file ke folder upload
                    $upload_dir = 'uploads/bukti_pembayaran/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    $upload_path = $upload_dir . $new_file_name;

                    if (move_uploaded_file($file_tmp, $upload_path)) {
                        // Simpan data ke database
                        $stmt = $koneksi->prepare("INSERT INTO guest (kepada, create_by, status, bukti_pembayaran) VALUES (?, ?, 'Pending', ?)");
                        $stmt->bind_param("sis", $kepada, $user_id, $new_file_name);

                        if ($stmt->execute()) {
                            // Redirect ke halaman utama setelah berhasil menambah data
                            header("Location: kartu_undangan.php");
                            exit(); // Menghentikan eksekusi kode setelah redirect
                        } else {
                            echo "Error: " . $stmt->error;
                        }
                    } else {
                        echo "Error: Gagal mengupload file.";
                    }
                } else {
                    echo "Error: File tidak valid.";
                }
            } else {
                echo "Error: File tidak ada.";
            }
        } else {
            // Tampilkan form untuk tambah undangan
?>
            <!DOCTYPE html>
            <html lang="id">

            <head>
                <meta charset="UTF-8">
                <title>Tambah Undangan</title>
                <style>
                    /* Styling untuk kontainer form */
                    .form-container {
                        max-width: 600px;
                        margin: 50px auto;
                        padding: 30px;
                        background-color: #fff;
                        border-radius: 10px;
                        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                        font-family: 'Arial', sans-serif;
                        border: 1px solid #ddd;
                    }

                    /* Styling heading */
                    .form-container h2 {
                        text-align: center;
                        color: #333;
                        font-size: 28px;
                        margin-bottom: 20px;
                    }

                    /* Styling untuk setiap grup form */
                    .form-group {
                        margin-bottom: 20px;
                    }

                    /* Styling label form */
                    .form-group label {
                        font-weight: bold;
                        display: block;
                        color: #555;
                        margin-bottom: 5px;
                    }

                    /* Styling input text dan input file */
                    .form-group input[type="text"],
                    .form-group input[type="file"] {
                        width: 100%;
                        padding: 12px;
                        margin-top: 8px;
                        border: 2px solid #ccc;
                        border-radius: 8px;
                        font-size: 16px;
                        transition: border-color 0.3s;
                    }

                    .form-group input[type="text"]:focus,
                    .form-group input[type="file"]:focus {
                        border-color: #007bff;
                        outline: none;
                    }

                    /* Styling tombol submit */
                    .form-group button {
                        width: 100%;
                        padding: 12px;
                        background-color: #007bff;
                        color: #fff;
                        border: none;
                        border-radius: 8px;
                        font-size: 18px;
                        font-weight: bold;
                        cursor: pointer;
                        transition: background-color 0.3s;
                    }

                    .form-group button:hover {
                        background-color: #0056b3;
                    }

                    /* Styling link kembali */
                    .back-link {
                        display: block;
                        text-align: center;
                        margin-top: 25px;
                        font-size: 16px;
                        color: #007bff;
                        text-decoration: none;
                    }

                    .back-link:hover {
                        text-decoration: underline;
                    }

                    /* Styling untuk form responsive pada layar kecil */
                    @media (max-width: 600px) {
                        .form-container {
                            padding: 20px;
                            width: 90%;
                        }

                        .form-container h2 {
                            font-size: 24px;
                        }

                        .form-group button {
                            font-size: 16px;
                        }
                    }
                </style>
            </head>

            <body>
                <div class="form-container">
                    <h2>Tambah Undangan</h2>

                    <form action="guest_crud.php?action=create" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="kepada">Kepada:</label>
                            <input type="text" name="kepada" id="kepada" required placeholder="Masukkan nama tujuan undangan">
                        </div>

                        <div class="form-group">
                            <label for="bukti_pembayaran">Bukti Pembayaran:</label>
                            <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" required accept="image/*,application/pdf">
                        </div>

                        <div class="form-group">
                            <button type="submit">Tambah</button>
                        </div>
                    </form>

                    <a href="kartu_undangan.php" class="back-link">Kembali ke Daftar Undangan</a>
                </div>
            </body>

            </html>
        <?php
        }
        break;

    case 'update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_guest = $_POST['id_guest'];
            $kepada = $_POST['kepada'];
            $bukti_pembayaran = $_FILES['bukti_pembayaran'];

            // Jika ada file baru yang diupload
            if ($bukti_pembayaran['error'] === 0) {
                $file_name = $bukti_pembayaran['name'];
                $file_tmp = $bukti_pembayaran['tmp_name'];
                $file_size = $bukti_pembayaran['size'];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                // Validasi ekstensi dan ukuran file
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];
                $max_size = 5 * 1024 * 1024; // Maksimum 5 MB

                if (in_array($file_ext, $allowed_extensions) && $file_size <= $max_size) {
                    // Tentukan nama file yang unik
                    $new_file_name = time() . '.' . $file_ext;

                    // Pindahkan file ke folder upload
                    $upload_dir = 'uploads/bukti_pembayaran/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    $upload_path = $upload_dir . $new_file_name;

                    if (move_uploaded_file($file_tmp, $upload_path)) {
                        // Update data ke database
                        $stmt = $koneksi->prepare("UPDATE guest SET kepada = ?, bukti_pembayaran = ? WHERE id_guest = ?");
                        $stmt->bind_param("ssi", $kepada, $new_file_name, $id_guest);

                        if ($stmt->execute()) {
                            header("Location: kartu_undangan.php");
                        } else {
                            echo "Error: " . $stmt->error;
                        }
                    } else {
                        echo "Error: Gagal mengupload file.";
                    }
                } else {
                    echo "Error: File tidak valid.";
                }
            } else {
                // Update data tanpa file
                $stmt = $koneksi->prepare("UPDATE guest SET kepada = ? WHERE id_guest = ?");
                $stmt->bind_param("si", $kepada, $id_guest);

                if ($stmt->execute()) {
                    header("Location: kartu_undangan.php");
                } else {
                    echo "Error: " . $stmt->error;
                }
            }
        } else {
            $id_guest = $_GET['id_guest'];
            $result = $koneksi->query("SELECT * FROM guest WHERE id_guest = $id_guest");
            $row = $result->fetch_assoc();
        ?>
            <!DOCTYPE html>
            <html lang="id">
            <head>
                <meta charset="UTF-8">
                <title>Edit Undangan</title>
                <style>
                    /* Styling untuk kontainer form */
                    .form-container {
                        max-width: 600px;
                        margin: 50px auto;
                        padding: 30px;
                        background-color: #fff;
                        border-radius: 10px;
                        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                        font-family: 'Arial', sans-serif;
                        border: 1px solid #ddd;
                    }

                    /* Styling heading */
                    .form-container h2 {
                        text-align: center;
                        color: #333;
                        font-size: 28px;
                        margin-bottom: 20px;
                    }

                    /* Styling untuk setiap grup form */
                    .form-group {
                        margin-bottom: 20px;
                    }

                    /* Styling label form */
                    .form-group label {
                        font-weight: bold;
                        display: block;
                        color: #555;
                        margin-bottom: 5px;
                    }

                    /* Styling input text dan input file */
                    .form-group input[type="text"],
                    .form-group input[type="file"] {
                        width: 100%;
                        padding: 12px;
                        margin-top: 8px;
                        border: 2px solid #ccc;
                        border-radius: 8px;
                        font-size: 16px;
                        transition: border-color 0.3s;
                    }

                    .form-group input[type="text"]:focus,
                    .form-group input[type="file"]:focus {
                        border-color: #007bff;
                        outline: none;
                    }

                    /* Styling tombol submit */
                    .form-group button {
                        width: 100%;
                        padding: 12px;
                        background-color: #007bff;
                        color: #fff;
                        border: none;
                        border-radius: 8px;
                        font-size: 18px;
                        font-weight: bold;
                        cursor: pointer;
                        transition: background-color 0.3s;
                    }

                    .form-group button:hover {
                        background-color: #0056b3;
                    }

                    /* Styling link kembali */
                    .back-link {
                        display: block;
                        text-align: center;
                        margin-top: 25px;
                        font-size: 16px;
                        color: #007bff;
                        text-decoration: none;
                    }

                    .back-link:hover {
                        text-decoration: underline;
                    }

                    /* Styling untuk form responsive pada layar kecil */
                    @media (max-width: 600px) {
                        .form-container {
                            padding: 20px;
                            width: 90%;
                        }

                        .form-container h2 {
                            font-size: 24px;
                        }

                        .form-group button {
                            font-size: 16px;
                        }
                    }
                </style>
            </head>
            <body>
                <div class="form-container">
                    <h2>Edit Undangan</h2>
                    <form action="guest_crud.php?action=update" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id_guest" value="<?= $row['id_guest'] ?>">

                        <div class="form-group">
                            <label for="kepada">Kepada:</label>
                            <input type="text" name="kepada" id="kepada" value="<?= htmlspecialchars($row['kepada']) ?>" required placeholder="Masukkan nama tujuan undangan">
                        </div>

                        <div class="form-group">
                            <label for="bukti_pembayaran">Bukti Pembayaran:</label>
                            <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" accept="image/*,application/pdf">
                        </div>

                        <div class="form-group">
                            <button type="submit">Simpan</button>
                        </div>
                    </form>

                    <a href="kartu_undangan.php" class="back-link">Kembali ke Daftar Undangan</a>
                </div>
            </body>
            </html>
<?php
        }
        break;

    case 'delete':
        $id_guest = $_GET['id_guest'];

        $stmt = $koneksi->prepare("DELETE FROM guest WHERE id_guest = ?");
        $stmt->bind_param("i", $id_guest);

        if ($stmt->execute()) {
            header("Location: kartu_undangan.php");
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        break;

    case 'approve':
        $id_guest = $_GET['id_guest'];
        $status = "Approved";

        $stmt = $koneksi->prepare("UPDATE guest SET status = ? WHERE id_guest = ?");
        $stmt->bind_param("si", $status, $id_guest);

        if ($stmt->execute()) {
            header("Location: kartu_undangan.php");
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        break;
}

$koneksi->close();
?>
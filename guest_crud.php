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
            $stmt = $koneksi->prepare("INSERT INTO guest (kepada, create_by, status) VALUES (?, ?, 'Pending')");
            $stmt->bind_param("si", $kepada, $user_id);

            if ($stmt->execute()) {
                // Redirect ke halaman utama setelah berhasil menambah data
                header("Location: kartu_undangan.php");
                exit(); // Menghentikan eksekusi kode setelah redirect
            } else {
                echo "Error: " . $stmt->error;
            }
    
            $stmt->close();
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
                        max-width: 500px;
                        margin: 50px auto;
                        padding: 20px;
                        border: 1px solid #ddd;
                        border-radius: 8px;
                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                        font-family: Arial, sans-serif;
                    }

                    .form-container h2 {
                        text-align: center;
                        color: #333;
                        font-size: 24px;
                        margin-bottom: 20px;
                    }

                    .form-group {
                        margin-bottom: 15px;
                    }

                    .form-group label {
                        font-weight: bold;
                        display: block;
                        color: #555;
                    }

                    .form-group input[type="text"] {
                        width: 96%;
                        padding: 10px;
                        margin-top: 5px;
                        border: 1px solid #ccc;
                        border-radius: 4px;
                        font-size: 16px;
                    }

                    .form-group button {
                        width: 100%;
                        padding: 10px;
                        background-color: #007bff;
                        color: #fff;
                        border: none;
                        border-radius: 4px;
                        font-size: 16px;
                        font-weight: bold;
                        cursor: pointer;
                        transition: background-color 0.3s ease;
                    }

                    .form-group button:hover {
                        background-color: #0056b3;
                    }

                    .back-link {
                        display: block;
                        text-align: center;
                        margin-top: 15px;
                        font-size: 14px;
                        color: #007bff;
                        text-decoration: none;
                    }

                    .back-link:hover {
                        text-decoration: underline;
                    }
                </style>
            </head>
            <body>
                <div class="form-container">
                    <h2>Tambah Undangan</h2>
                    <form action="guest_crud.php?action=create" method="post">
                        <div class="form-group">
                            <label for="kepada">Kepada:</label>
                            <input type="text" name="kepada" id="kepada" required placeholder="Masukkan nama tujuan undangan">
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

    case 'read':
        // Ambil data undangan dari database untuk ditampilkan
        $result = $koneksi->query("SELECT * FROM guest WHERE create_by = $user_id");
        ?>

        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>CRUD Undangan</title>
        </head>
        <body>
            <h2>Tambah Undangan</h2>
            <form action="guest_crud.php?action=create" method="post">
                <label for="kepada">Kepada:</label>
                <input type="text" name="kepada" id="kepada" required>
                <button type="submit">Tambah</button>
            </form>

            <h2>Daftar Undangan</h2>
            <table border="1">
                <tr>
                    <th>No</th>
                    <th>Kepada</th>
                    <th>Dibuat</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                <?php
                $no = 1;
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . htmlspecialchars($row['kepada']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                    echo "<td>
                        <a href='guest_crud.php?action=update&id_guest=" . $row['id_guest'] . "'>Edit</a> |
                        <a href='guest_crud.php?action=delete&id_guest=" . $row['id_guest'] . "'>Hapus</a>";
                    if ($row['status'] == 'Pending') {
                        echo " | <a href='guest_crud.php?action=approve&id_guest=" . $row['id_guest'] . "'>Setujui</a>";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </body>
        </html>

        <?php
        break;

    case 'update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_guest = $_POST['id_guest'];
            $kepada = $_POST['kepada'];

            $stmt = $koneksi->prepare("UPDATE guest SET kepada = ? WHERE id_guest = ?");
            $stmt->bind_param("si", $kepada, $id_guest);

            if ($stmt->execute()) {
                header("Location: kartu_undangan.php");
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
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
            </head>
            <body>
                <h2>Edit Undangan</h2>
                <form action="guest_crud.php?action=update" method="post">
                    <input type="hidden" name="id_guest" value="<?= $row['id_guest'] ?>">
                    <label for="kepada">Kepada:</label>
                    <input type="text" name="kepada" id="kepada" value="<?= htmlspecialchars($row['kepada']) ?>" required>
                    <button type="submit">Simpan</button>
                </form>
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

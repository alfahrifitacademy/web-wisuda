<?php
session_start();
require 'db_connection.php';

// Cek apakah pengguna sudah login dan merupakan admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Ambil data undangan yang belum disetujui
$pendingInvitations = $conn->query("SELECT * FROM guest WHERE status = 'Pending'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Approve Undangan</title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
    <div class="container">
        <div class="navigation">
            <!-- Navigation khusus admin -->
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="kartu_undangan.php">Kartu Undangan</a></li>
                <li><a href="admin_dashboard.php">Approve Undangan</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>

        <div class="main">
            <div class="topbar">
                <h2>Approve Undangan</h2>
            </div>

            <div class="content-container">
                <h3>Daftar Undangan Pending</h3>

                <div class="table-container">
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
                        while ($row = $pendingInvitations->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td>" . htmlspecialchars($row['kepada']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                            echo "<td>
                                <a href='admin_dashboard.php?action=approve&id_guest=" . $row['id_guest'] . "'>Setujui</a> |
                                <a href='admin_dashboard.php?action=reject&id_guest=" . $row['id_guest'] . "'>Tolak</a>
                            </td>";
                            echo "</tr>";
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/dashboard.js"></script>
</body>
</html>

<?php
// Proses approve atau reject jika ada aksi dari admin
if (isset($_GET['action']) && isset($_GET['id_guest'])) {
    $action = $_GET['action'];
    $id_guest = $_GET['id_guest'];

    if ($action == 'approve') {
        $status = 'Approved';
    } elseif ($action == 'reject') {
        $status = 'Rejected';
    } else {
        $status = null;
    }

    if ($status) {
        $stmt = $conn->prepare("UPDATE guest SET status = ? WHERE id_guest = ?");
        $stmt->bind_param("si", $status, $id_guest);

        if ($stmt->execute()) {
            // Redirect ke halaman yang sama untuk refresh data
            header("Location: admin_dashboard.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

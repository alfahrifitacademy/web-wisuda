<?php
session_start(); // Memulai session

// Koneksi ke database
include 'admin/db_connnection.php';

// Cek apakah form login telah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $NIM = $_POST['NIM'];
    $password = $_POST['password'];

    // Query untuk mendapatkan data pengguna berdasarkan NIM
    $query = "SELECT * FROM users WHERE NIM = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("s", $NIM);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Cek apakah password sesuai
        if (password_verify($password, $user['password'])) {
            // Set session dan arahkan ke halaman dashboard
            $_SESSION['user_id'] = $user['id_users'];
            $_SESSION['nama'] = $user['nama'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "NIM tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/style_regist.css">
</head>
<body>

<div class="form-container">
    <h2>Login</h2>
    <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>
    <form action="login.php" method="POST">
        <input type="text" name="NIM" placeholder="NIM" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
        <a href="register.php">Belum punya akun? Daftar</a>
    </form>
</div>

</body>
</html>

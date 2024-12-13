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

// Mengambil pesan sukses dari session jika ada
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';

// Menghapus pesan setelah ditampilkan
unset($_SESSION['success_message']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/style_regist.css">
</head>

<body>
    <div class="alert-container">
        <!-- Tampilkan pesan jika ada -->
        <?php if ($success_message): ?>
            <div class="alert success">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="form-container">
        <h2>Login</h2>
        <?php if (isset($error)) {
            echo "<p style='color: red;'>$error</p>";
        } ?>
        <form action="login.php" method="POST">
            <input type="text" name="NIM" placeholder="NIM" required>
            <div class="password-container">
                <input type="password" id="password" name="password" placeholder="Password" required>
                <span id="togglePassword" class="toggle-password">
                    <ion-icon name="eye-outline"></ion-icon>
                </span>
            </div>
            <button type="submit">Login</button>
            <a href="register.php">Belum punya akun? Daftar</a>
        </form>
    </div>

    <script>
        const togglePassword = document.querySelector("#togglePassword");
        const passwordField = document.querySelector("#password");

        togglePassword.addEventListener("click", function() {
            // Toggle the type attribute
            const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
            passwordField.setAttribute("type", type);

            // Toggle the icon
            this.innerHTML = type === "password" ?
                '<ion-icon name="eye-outline"></ion-icon>' :
                '<ion-icon name="eye-off-outline"></ion-icon>';
        });
    </script>

</body>

</html>
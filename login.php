<?php
session_start();

// Koneksi ke database
$conn = new mysqli('localhost', 'root', '', 'undangan_wisuda');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Proses pendaftaran
if (isset($_POST['register'])) {
    if (isset($_POST['nama']) && isset($_POST['nim']) && isset($_POST['password'])) {
        $nama = $conn->real_escape_string($_POST['nama']);
        $nim = $conn->real_escape_string($_POST['nim']);
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $sql = "INSERT INTO user (nama, nim, password) VALUES ('$nama', '$nim', '$password')";

        if ($conn->query($sql) === TRUE) {
            header("Location: login.html");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Semua field harus diisi.";
    }
}

// Proses login
if (isset($_POST['login'])) {
    // Cek apakah field username dan password diisi
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        $username = $conn->real_escape_string($_POST['username']);
        $password = $_POST['password'];

        $sql = "SELECT * FROM user WHERE nim='$username'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['username'] = $row['nama'];
                header("Location: dashboard.php");
                exit();
            } else {
                echo "Password salah!";
            }
        } else {
            echo "Username tidak ditemukan!";
        }
    } else {
        echo "Semua field harus diisi.";
    }
}


$conn->close();
?>

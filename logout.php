<?php
// Memulai session
session_start();

// Menghapus semua session
session_unset(); // Menghapus variabel-variabel session
session_destroy(); // Menghancurkan session

// Mengarahkan pengguna kembali ke halaman login setelah logout
header("Location: index.html");
exit();
?>

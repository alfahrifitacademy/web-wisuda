<?php
include '../admin/db_connnection.php';

$sql = "SELECT users.id_users, users.nama, users.npm, fakultas.fakultas, jurusan.jurusan, users.is_admin, users.created_at 
        FROM users AS users
        LEFT JOIN fakultas AS fakultas ON users.fakultas = fakultas.id_fakultas
        LEFT JOIN jurusan AS jurusan ON users.jurusan = jurusan.id_jurusan";
$result = $koneksi->query($sql);
?>

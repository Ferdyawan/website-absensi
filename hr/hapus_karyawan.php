<?php
include '../config/db.php';

if (!isset($_GET['id'])) {
    die("ID tidak ditemukan");
}

$id = $_GET['id'];

// hapus dari tabel karyawan
mysqli_query($conn, "DELETE FROM karyawan WHERE user_id='$id'");

// hapus dari tabel users
mysqli_query($conn, "DELETE FROM users WHERE id='$id'");

header("Location: dashboard.php");
exit;

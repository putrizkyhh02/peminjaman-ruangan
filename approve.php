<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] !== 'admin') {
    header("Location: home.php");
    exit;
}

include 'config/koneksi.php';

$id = (int) $_GET['id'];

$stmt = mysqli_prepare($conn, "UPDATE peminjaman SET status = 'disetujui' WHERE id_pinjam = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

header("Location: peminjaman.php");
exit;
?>
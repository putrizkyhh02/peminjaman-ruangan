<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
if ($_SESSION['role'] !== 'admin') {
    echo "<script>alert('Hanya admin yang dapat mengubah status.');location.href='peminjaman.php';</script>";
    exit;
}

include 'config/koneksi.php';
$id = (int) ($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';
$status_valid = ['pending', 'disetujui', 'approve'];

if ($id < 1 || !in_array($status, $status_valid, true)) {
    echo "<script>alert('Status tidak valid.');location.href='peminjaman.php';</script>";
    exit;
}

$stmt = mysqli_prepare($conn, "UPDATE peminjaman SET status=? WHERE id_pinjam=?");
mysqli_stmt_bind_param($stmt, "si", $status, $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
header("Location: peminjaman.php");
exit;

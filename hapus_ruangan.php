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

$stmt1 = mysqli_prepare($conn, "DELETE FROM peminjaman WHERE id_ruangan = ?");
mysqli_stmt_bind_param($stmt1, "i", $id);
mysqli_stmt_execute($stmt1);
mysqli_stmt_close($stmt1);

$stmt2 = mysqli_prepare($conn, "DELETE FROM ruangan WHERE id_ruangan = ?");
mysqli_stmt_bind_param($stmt2, "i", $id);
mysqli_stmt_execute($stmt2);
mysqli_stmt_close($stmt2);

header("Location: ruangan.php");
exit;
?>

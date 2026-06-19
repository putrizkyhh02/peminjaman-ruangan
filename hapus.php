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

$cek = mysqli_prepare($conn, "SELECT role FROM users WHERE id_user=?");
mysqli_stmt_bind_param($cek, "i", $id);
mysqli_stmt_execute($cek);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($cek));
mysqli_stmt_close($cek);
if ($user && $user['role'] === 'admin') {
    echo "<script>alert('Akun admin utama tidak dapat dihapus.');location.href='index.php';</script>";
    exit;
}

$stmt1 = mysqli_prepare($conn, "DELETE FROM peminjaman WHERE id_user = ?");
mysqli_stmt_bind_param($stmt1, "i", $id);
mysqli_stmt_execute($stmt1);
mysqli_stmt_close($stmt1);

$stmt2 = mysqli_prepare($conn, "DELETE FROM users WHERE id_user = ?");
mysqli_stmt_bind_param($stmt2, "i", $id);
mysqli_stmt_execute($stmt2);
mysqli_stmt_close($stmt2);

header("Location: index.php");
exit;
?>

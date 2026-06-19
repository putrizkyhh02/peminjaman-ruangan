<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

include 'config/koneksi.php';

$id = (int) $_GET['id'];
$user_id = (int) $_SESSION['id_user'];
$role = $_SESSION['role'];

// Fetch booking data to verify authorization and status
$stmt_get = mysqli_prepare($conn, "SELECT * FROM peminjaman WHERE id_pinjam = ?");
mysqli_stmt_bind_param($stmt_get, "i", $id);
mysqli_stmt_execute($stmt_get);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_get));
mysqli_stmt_close($stmt_get);

if (!$data) {
    echo "<script>alert('Peminjaman tidak ditemukan!'); window.location.href='peminjaman.php';</script>";
    exit;
}

$allowed = $role === 'admin' || $data['id_user'] == $user_id;

if ($allowed) {
    $stmt_del = mysqli_prepare($conn, "DELETE FROM peminjaman WHERE id_pinjam = ?");
    mysqli_stmt_bind_param($stmt_del, "i", $id);
    
    if (mysqli_stmt_execute($stmt_del)) {
        mysqli_stmt_close($stmt_del);
        header("Location: peminjaman.php");
        exit;
    } else {
        mysqli_stmt_close($stmt_del);
        echo "<script>alert('Gagal menghapus peminjaman!'); window.location.href='peminjaman.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('Anda tidak diizinkan menghapus/membatalkan peminjaman ini!'); window.location.href='peminjaman.php';</script>";
    exit;
}
?>

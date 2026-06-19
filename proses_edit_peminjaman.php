<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
include 'config/koneksi.php';
$id = (int) ($_POST['id'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT * FROM peminjaman WHERE id_pinjam=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$lama = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);
$boleh = $lama && ($_SESSION['role'] === 'admin' || $lama['id_user'] == $_SESSION['id_user']);
if (!$boleh) {
    echo "<script>alert('Anda tidak diizinkan mengubah data ini.');location.href='peminjaman.php';</script>";
    exit;
}
$nama = trim($_POST['nama_peminjam'] ?? '');
$prodi = trim($_POST['program_studi'] ?? '');
$npm = trim($_POST['npm'] ?? '');
$ruangan = trim($_POST['nama_ruangan'] ?? '');
$tanggal = $_POST['tanggal'] ?? '';
$mulai = $_POST['jam_mulai'] ?? '';
$selesai = $_POST['jam_selesai'] ?? '';
$keperluan = trim($_POST['keperluan'] ?? '');
$status = $_SESSION['role'] === 'admin' ? ($_POST['status'] ?? 'pending') : $lama['status'];
if (in_array('', [$nama,$prodi,$npm,$ruangan,$tanggal,$mulai,$selesai,$keperluan], true) || $mulai >= $selesai || !in_array($status, ['pending','disetujui','approve'], true)) {
    echo "<script>alert('Data belum lengkap atau waktu tidak valid.');history.back();</script>";
    exit;
}
$cek = mysqli_prepare($conn, "SELECT id_pinjam FROM peminjaman WHERE id_pinjam<>? AND LOWER(nama_ruangan)=LOWER(?) AND tanggal=? AND status IN ('disetujui','approve') AND jam_mulai<? AND jam_selesai>?");
mysqli_stmt_bind_param($cek, "issss", $id, $ruangan, $tanggal, $selesai, $mulai);
mysqli_stmt_execute($cek);
$bentrok = mysqli_fetch_assoc(mysqli_stmt_get_result($cek));
mysqli_stmt_close($cek);
if ($bentrok) {
    echo "<script>alert('Ruangan sudah dipakai pada waktu yang bertabrakan.');history.back();</script>";
    exit;
}
$update = mysqli_prepare($conn, "UPDATE peminjaman SET nama_peminjam=?,program_studi=?,npm=?,nama_ruangan=?,tanggal=?,jam_mulai=?,jam_selesai=?,keperluan=?,status=? WHERE id_pinjam=?");
mysqli_stmt_bind_param($update, "sssssssssi", $nama,$prodi,$npm,$ruangan,$tanggal,$mulai,$selesai,$keperluan,$status,$id);
if (!mysqli_stmt_execute($update)) {
    die("Gagal mengubah data: ".mysqli_error($conn));
}
mysqli_stmt_close($update);
header("Location: peminjaman.php");
exit;

<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$id_user = (int) $_SESSION['id_user'];
$nama_peminjam = trim($_POST['nama_peminjam'] ?? '');
$program_studi = trim($_POST['program_studi']);
$npm = trim($_POST['npm']);
$id_ruangan = 0;
$nama_ruangan = trim($_POST['nama_ruangan'] ?? '');
$tanggal = $_POST['tanggal'];
$jam_mulai = $_POST['jam_mulai'];
$jam_selesai = $_POST['jam_selesai'];
$keperluan = trim($_POST['keperluan']);

if ($id_user == 0 || $nama_peminjam == "" || $program_studi == "" || $npm == "" || $nama_ruangan == "" || $tanggal == "" || $jam_mulai == "" || $jam_selesai == "" || $keperluan == "") {
    echo "<script>alert('Semua field wajib diisi!'); window.history.back();</script>";
    exit;
}

// Validate Time Range (start must be before end)
if ($jam_mulai >= $jam_selesai) {
    echo "<script>alert('Jam mulai harus lebih awal dari jam selesai!'); window.history.back();</script>";
    exit;
}

// 1. Check double booking for the same room, date, and overlapping time slots
// Criteria for overlap: existing_start < input_end AND existing_end > input_start
$stmt_check = mysqli_prepare($conn, "
    SELECT p.*
    FROM peminjaman p
    WHERE LOWER(p.nama_ruangan) = LOWER(?)
      AND p.tanggal = ? 
      AND p.status IN ('disetujui', 'approve')
      AND p.jam_mulai < ? 
      AND p.jam_selesai > ?
");
mysqli_stmt_bind_param($stmt_check, "ssss", $nama_ruangan, $tanggal, $jam_selesai, $jam_mulai);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);
$conflict = mysqli_fetch_assoc($result_check);
mysqli_stmt_close($stmt_check);

if ($conflict) {
    $msg = "Ruangan " . htmlspecialchars($conflict['nama_ruangan']) . " sudah disetujui untuk dipinjam pada tanggal " . htmlspecialchars($tanggal) . " pukul " . htmlspecialchars($conflict['jam_mulai']) . " - " . htmlspecialchars($conflict['jam_selesai']) . ". Silakan pilih waktu atau ruangan lain.";
    echo "<script>alert('" . addslashes($msg) . "'); window.history.back();</script>";
    exit;
}

// 2. Insert new reservation (defaults to 'pending')
$stmt_insert = mysqli_prepare($conn, "
    INSERT INTO peminjaman (id_user, nama_peminjam, program_studi, npm, id_ruangan, nama_ruangan, tanggal, jam_mulai, jam_selesai, keperluan, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
");
mysqli_stmt_bind_param($stmt_insert, "isssisssss", $id_user, $nama_peminjam, $program_studi, $npm, $id_ruangan, $nama_ruangan, $tanggal, $jam_mulai, $jam_selesai, $keperluan);

if (mysqli_stmt_execute($stmt_insert)) {
    mysqli_stmt_close($stmt_insert);
    header("Location: peminjaman.php");
    exit;
} else {
    mysqli_stmt_close($stmt_insert);
    echo "<script>alert('Gagal mengajukan peminjaman!'); window.history.back();</script>";
    exit;
}
?>

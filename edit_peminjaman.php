<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
include 'config/koneksi.php';
$id = (int) ($_GET['id'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT p.*, COALESCE(NULLIF(p.nama_peminjam,''),u.nama) nama_tampil, COALESCE(NULLIF(p.nama_ruangan,''),r.nama_ruangan) ruangan_tampil FROM peminjaman p LEFT JOIN users u ON p.id_user=u.id_user LEFT JOIN ruangan r ON p.id_ruangan=r.id_ruangan WHERE p.id_pinjam=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);
$boleh = $data && ($_SESSION['role'] === 'admin' || $data['id_user'] == $_SESSION['id_user']);
if (!$boleh) {
    echo "<script>alert('Data tidak ditemukan atau Anda tidak diizinkan mengeditnya.');location.href='peminjaman.php';</script>";
    exit;
}
?>
<link rel="stylesheet" href="style.css">
<div class="navbar"><div class="nav-left"><a href="home.php">Home</a><a href="peminjaman.php" class="active">Peminjaman</a></div><div class="nav-right"><a href="logout.php">Logout</a></div></div>
<h2>Edit Peminjaman</h2>
<div class="form-box"><form method="POST" action="proses_edit_peminjaman.php">
<input type="hidden" name="id" value="<?= $data['id_pinjam']; ?>">
<label>Nama Peminjam</label><input type="text" name="nama_peminjam" value="<?= htmlspecialchars($data['nama_tampil']); ?>" required>
<label>Program Studi</label><input type="text" name="program_studi" value="<?= htmlspecialchars($data['program_studi']); ?>" required>
<label>NPM</label><input type="text" name="npm" value="<?= htmlspecialchars($data['npm']); ?>" required>
<label>Ruangan / Laboratorium</label><input type="text" name="nama_ruangan" value="<?= htmlspecialchars($data['ruangan_tampil']); ?>" required>
<label>Tanggal</label><input type="date" name="tanggal" value="<?= htmlspecialchars($data['tanggal']); ?>" required>
<label>Jam Mulai</label><input type="time" name="jam_mulai" value="<?= substr($data['jam_mulai'],0,5); ?>" required>
<label>Jam Selesai</label><input type="time" name="jam_selesai" value="<?= substr($data['jam_selesai'],0,5); ?>" required>
<label>Keperluan</label><input type="text" name="keperluan" value="<?= htmlspecialchars($data['keperluan']); ?>" required>
<?php if ($_SESSION['role'] === 'admin') { ?>
<label>Status</label><select name="status"><option value="pending" <?= $data['status']==='pending'?'selected':''; ?>>Pending</option><option value="disetujui" <?= $data['status']==='disetujui'?'selected':''; ?>>Disetujui</option><option value="approve" <?= $data['status']==='approve'?'selected':''; ?>>Approve</option></select>
<?php } ?>
<button type="submit">Update</button>
</form></div>

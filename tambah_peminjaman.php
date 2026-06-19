<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

include 'config/koneksi.php';

?>

<link rel="stylesheet" href="style.css">

<div class="navbar">
    <div class="nav-left">
        <a href="home.php">Home</a>
        <a class="nav-icon active" href="peminjaman.php">Peminjaman</a>
        <?php if ($_SESSION['role'] === 'admin') { ?>
            <a class="nav-icon" href="index.php">Users</a>
            <a class="nav-icon" href="ruangan.php">Ruangan</a>
        <?php } ?>
    </div>
    <div class="nav-right">
        <a href="logout.php">Logout</a>
    </div>
</div>

<h2>Tambah Peminjaman</h2>

<div class="form-box">
<form method="POST" action="proses_tambah_peminjaman.php">

    <label>Nama Peminjam</label>
    <input type="text" name="nama_peminjam" placeholder="Ketik nama peminjam bebas..." required>

    <!-- PROGRAM STUDI -->
    <label>Program Studi</label>
    <input type="text" name="program_studi" placeholder="Masukkan program studi..." required>

    <!-- NPM -->
    <label>NPM</label>
    <input type="text" name="npm" placeholder="Masukkan NPM..." required>

    <!-- RUANGAN -->
    <label>Ruangan / Laboratorium</label>
    <input type="text" name="nama_ruangan" placeholder="Ketik nama ruangan atau laboratorium bebas..." required>

    <!-- TANGGAL -->
    <label>Tanggal</label>
    <input type="date" name="tanggal" required>

    <!-- JAM MULAI -->
    <label>Jam Mulai</label>
    <input type="time" name="jam_mulai" required>

    <!-- JAM SELESAI -->
    <label>Jam Selesai</label>
    <input type="time" name="jam_selesai" required>

    <!-- KEPERLUAN -->
    <label>Keperluan</label>
    <input type="text" name="keperluan" placeholder="Masukkan keperluan peminjaman (contoh: Praktikum, Rapat)..." required>

    <button type="submit">Simpan</button>

</form>
</div>

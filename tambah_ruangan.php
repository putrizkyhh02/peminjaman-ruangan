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

if (isset($_POST['submit'])) {
    $nama_ruangan = trim($_POST['nama_ruangan']);
    $lokasi = trim($_POST['lokasi']);
    $kapasitas = (int) $_POST['kapasitas'];

    $stmt = mysqli_prepare($conn, "INSERT INTO ruangan (nama_ruangan, lokasi, kapasitas) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssi", $nama_ruangan, $lokasi, $kapasitas);
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: ruangan.php");
        exit;
    } else {
        mysqli_stmt_close($stmt);
        echo "<script>alert('Gagal menambah ruangan!');</script>";
    }
}
?>

<link rel="stylesheet" href="style.css">

<div class="navbar">
    <div class="nav-left">
        <a href="home.php">Home</a>
        <a class="nav-icon" href="peminjaman.php">Peminjaman</a>
        <?php if ($_SESSION['role'] === 'admin') { ?>
            <a class="nav-icon" href="index.php">Users</a>
            <a class="nav-icon active" href="ruangan.php">Ruangan</a>
        <?php } ?>
    </div>
    <div class="nav-right">
        <a href="logout.php">Logout</a>
    </div>
</div>

<h2>Tambah Ruangan</h2>

<div class="form-box">
    <form method="POST">
        <label>Nama Ruangan</label>
        <input type="text" name="nama_ruangan" required>

        <label>Lokasi</label>
        <input type="text" name="lokasi" required>

        <label>Kapasitas</label>
        <input type="number" name="kapasitas" min="1" required>

        <button type="submit" name="submit">Simpan</button>
    </form>
</div>

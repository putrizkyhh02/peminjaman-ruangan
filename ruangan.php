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
?>

<link rel="stylesheet" href="style.css">

<!-- NAVBAR -->
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

<!-- WELCOME -->
<p>Welcome, <b><?= $_SESSION['nama']; ?></b> 👋</p>

<h2>Data Ruangan</h2>

<!-- BUTTON -->
<a href="tambah_ruangan.php">+ Tambah Ruangan</a><br><br>

<?php
$query = mysqli_query($conn, "SELECT * FROM ruangan");
?>

<table>
    <tr>
        <th>ID</th>
        <th>Nama Ruangan</th>
        <th>Lokasi</th>
        <th>Kapasitas</th>
        <th>Aksi</th>
    </tr>

    <?php while($data = mysqli_fetch_assoc($query)) { ?>
    <tr>
        <td><?= $data['id_ruangan']; ?></td>
        <td><?= $data['nama_ruangan']; ?></td>
        <td><?= $data['lokasi']; ?></td>
        <td><?= $data['kapasitas']; ?></td>
        <td>
            <a class="btn-approve" href="edit_ruangan.php?id=<?= $data['id_ruangan']; ?>">Edit</a>
            <a class="btn-delete" href="hapus_ruangan.php?id=<?= $data['id_ruangan']; ?>" 
            onclick="return confirm('Yakin mau hapus?')">Hapus</a>
        </td>
    </tr>
    <?php } ?>
</table>
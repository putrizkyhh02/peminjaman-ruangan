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
            <a class="nav-icon active" href="index.php">Users</a>
            <a class="nav-icon" href="ruangan.php">Ruangan</a>
        <?php } ?>
    </div>
    <div class="nav-right">
        <a href="logout.php">Logout</a>
    </div>
</div>

<!-- WELCOME -->
<p>Welcome, <b><?= $_SESSION['nama']; ?></b> 👋</p>

<!-- BUTTON -->
<a href="tambah.php">+ Tambah Data</a><br><br>

<h2>Data Users</h2>

<?php
$query = mysqli_query($conn, "SELECT * FROM users");
?>

<table>
    <tr>
        <th>ID</th>
        <th>Nama</th>
        <th>Email</th>
        <th>Role</th>
        <th>Aksi</th>
    </tr>

    <?php while($data = mysqli_fetch_assoc($query)) { ?>
    <tr>
        <td><?= $data['id_user']; ?></td>
        <td><?= $data['nama']; ?></td>
        <td><?= isset($data['email']) ? $data['email'] : ''; ?></td>
        <td><?= $data['role']; ?></td>
        <td>
            <a class="btn-approve" href="edit.php?id=<?= $data['id_user']; ?>">Edit</a>
            <a class="btn-delete" href="hapus.php?id=<?= $data['id_user']; ?>" 
            onclick="return confirm('Yakin mau hapus?')">Hapus</a>
        </td>
    </tr>
    <?php } ?>
</table>

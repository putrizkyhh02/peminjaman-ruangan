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

// Get room data using prepared statement
$stmt_get = mysqli_prepare($conn, "SELECT * FROM ruangan WHERE id_ruangan = ?");
mysqli_stmt_bind_param($stmt_get, "i", $id);
mysqli_stmt_execute($stmt_get);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_get));
mysqli_stmt_close($stmt_get);

if (!$data) {
    echo "Ruangan tidak ditemukan!";
    exit;
}

if (isset($_POST['submit'])) {
    $nama_ruangan = trim($_POST['nama_ruangan']);
    $lokasi = trim($_POST['lokasi']);
    $kapasitas = (int) $_POST['kapasitas'];

    if ($nama_ruangan == "" || $lokasi == "") {
        echo "<script>alert('Nama ruangan dan lokasi tidak boleh kosong!');</script>";
    } else {
        $stmt_update = mysqli_prepare($conn, "UPDATE ruangan SET nama_ruangan = ?, lokasi = ?, kapasitas = ? WHERE id_ruangan = ?");
        mysqli_stmt_bind_param($stmt_update, "ssii", $nama_ruangan, $lokasi, $kapasitas, $id);

        if (mysqli_stmt_execute($stmt_update)) {
            mysqli_stmt_close($stmt_update);
            header("Location: ruangan.php");
            exit;
        } else {
            mysqli_stmt_close($stmt_update);
            echo "<script>alert('Gagal mengubah data ruangan!');</script>";
        }
    }
}
?>

<link rel="stylesheet" href="style.css">

<div class="navbar">
    <div class="nav-left">
        <a href="peminjaman.php">Peminjaman</a>
        <a href="index.php">Users</a>
        <a href="ruangan.php" class="active">Ruangan</a>
    </div>
    <div class="nav-right">
        <a href="logout.php">Logout</a>
    </div>
</div>

<h2>Edit Ruangan</h2>

<div class="form-box">
    <form method="POST">
        <label>Nama Ruangan</label>
        <input type="text" name="nama_ruangan" value="<?= htmlspecialchars($data['nama_ruangan']); ?>" required>

        <label>Lokasi</label>
        <input type="text" name="lokasi" value="<?= htmlspecialchars($data['lokasi']); ?>" required>

        <label>Kapasitas</label>
        <input type="number" name="kapasitas" value="<?= $data['kapasitas']; ?>" min="1" required>

        <button type="submit" name="submit">Update</button>
    </form>
</div>

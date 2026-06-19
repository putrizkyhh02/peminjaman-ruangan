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

$cek_email = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'email'");
if (mysqli_num_rows($cek_email) == 0) {
    mysqli_query($conn, "ALTER TABLE users ADD email VARCHAR(100) NOT NULL AFTER nama");
}

if (isset($_POST['submit'])) {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $role = 'mahasiswa';
    $password = $_POST['password'];

    if ($nama == "" || $email == "" || $password == "") {
        echo "<script>alert('Semua field harus diisi!');</script>";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = mysqli_prepare($conn, "INSERT INTO users (nama, email, role, password) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $nama, $email, $role, $hashed_password);
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: index.php");
            exit;
        } else {
            mysqli_stmt_close($stmt);
            echo "<script>alert('Gagal menambah user!');</script>";
        }
    }
}
?>

<link rel="stylesheet" href="style.css">

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

<h2>Tambah Data User</h2>

<div class="form-box">
    <form method="POST">
        <label>Nama</label>
        <input type="text" name="nama" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Role</label>
        <input type="text" value="Mahasiswa / User" disabled>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit" name="submit">Simpan</button>
    </form>
</div>

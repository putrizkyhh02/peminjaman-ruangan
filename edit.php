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

$id = (int) $_GET['id'];

// Get user data using prepared statement
$stmt_get = mysqli_prepare($conn, "SELECT * FROM users WHERE id_user = ?");
mysqli_stmt_bind_param($stmt_get, "i", $id);
mysqli_stmt_execute($stmt_get);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_get));
mysqli_stmt_close($stmt_get);

if (!$data) {
    echo "User tidak ditemukan!";
    exit;
}

if (isset($_POST['submit'])) {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $role = $data['role'] === 'admin' ? 'admin' : 'mahasiswa';
    $password = $_POST['password'];

    if ($password == "") {
        $stmt_update = mysqli_prepare($conn, "UPDATE users SET nama = ?, email = ?, role = ? WHERE id_user = ?");
        mysqli_stmt_bind_param($stmt_update, "sssi", $nama, $email, $role, $id);
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt_update = mysqli_prepare($conn, "UPDATE users SET nama = ?, email = ?, role = ?, password = ? WHERE id_user = ?");
        mysqli_stmt_bind_param($stmt_update, "ssssi", $nama, $email, $role, $hashed_password, $id);
    }

    if (mysqli_stmt_execute($stmt_update)) {
        mysqli_stmt_close($stmt_update);
        
        if (isset($_SESSION['id_user']) && $_SESSION['id_user'] == $id) {
            $_SESSION['nama'] = $nama;
            $_SESSION['role'] = $role;
        }

        header("Location: index.php");
        exit;
    } else {
        mysqli_stmt_close($stmt_update);
        echo "<script>alert('Gagal mengupdate user!');</script>";
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

<h2>Edit Data User</h2>

<div class="form-box">
    <form method="POST">
        <label>Nama</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']); ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($data['email']); ?>" required>

        <label>Role</label>
        <input type="text" value="<?= $data['role'] === 'admin' ? 'Admin (akun utama)' : 'Mahasiswa / User'; ?>" disabled>

        <label>Password Baru</label>
        <input type="password" name="password" placeholder="Kosongkan jika tidak diubah">

        <button type="submit" name="submit">Update</button>
    </form>
</div>

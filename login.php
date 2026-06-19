<?php
session_start();
include 'config/koneksi.php';

$cek_email = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'email'");
if (mysqli_num_rows($cek_email) == 0) {
    mysqli_query($conn, "ALTER TABLE users ADD email VARCHAR(100) NOT NULL AFTER nama");
}

if (isset($_POST['login'])) {
    $nama = trim($_POST['nama']);
    $password = $_POST['password'];

    if ($nama == "" || $password == "") {
        echo "<script>alert('Nama dan Password wajib diisi!');</script>";
    } else {
        // Prepared statement to find the user
        $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE nama = ?");
        mysqli_stmt_bind_param($stmt, "s", $nama);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($data) {
            $password_ok = false;
            // Verify BCRYPT hash
            if (password_verify($password, $data['password'])) {
                $password_ok = true;
            } elseif ($password === $data['password']) {
                // Migrate plaintext password to hash
                $password_ok = true;
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt_update = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id_user = ?");
                mysqli_stmt_bind_param($stmt_update, "si", $hashed, $data['id_user']);
                mysqli_stmt_execute($stmt_update);
                mysqli_stmt_close($stmt_update);
            }

            if ($password_ok) {
                $_SESSION['login'] = true;
                $_SESSION['id_user'] = $data['id_user'];
                $_SESSION['nama'] = $data['nama'];
                $_SESSION['role'] = $data['role'];

                header("Location: home.php");
                exit;
            } else {
                echo "<script>alert('Login gagal! Nama atau password salah');</script>";
            }
        } else {
            echo "<script>alert('Login gagal! Nama atau password salah');</script>";
        }
    }
}
?>

<link rel="stylesheet" href="style.css?v=20260619-login-campus-colors">

<div class="login-body login-campus-bg" style="background-image: linear-gradient(120deg, rgba(3, 15, 30, 0.72), rgba(6, 35, 43, 0.42)), url('assets/kampus-polinela-home.png');">
    <div class="login-campus-intro">
        <img src="assets/logo-polinela.png" alt="Logo Polinela">
        <div>
            <span>POLITEKNIK NEGERI LAMPUNG</span>
            <h1>Sistem Peminjaman Ruangan</h1>
            <p>Ajukan dan kelola peminjaman ruang kelas maupun laboratorium dengan lebih mudah.</p>
        </div>
    </div>

    <div class="login-box">
        <p class="login-eyebrow">Selamat datang</p>
        <h2>Masuk</h2>

        <form method="POST">
            <label>Nama</label>
            <input type="text" name="nama" placeholder="Masukkan nama">

            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan password">

            <button type="submit" name="login">Masuk</button>
        </form>

        <p class="login-link">Belum punya akun? <a href="register.php">Daftar akun</a></p>
    </div>
</div>

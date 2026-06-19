<?php
session_start();
include 'config/koneksi.php';

$cek_email = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'email'");
if (mysqli_num_rows($cek_email) == 0) {
    mysqli_query($conn, "ALTER TABLE users ADD email VARCHAR(100) NOT NULL AFTER nama");
}

if (isset($_POST['register'])) {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($nama == "" || $email == "" || $password == "") {
        echo "<script>alert('Nama, Email, dan Password wajib diisi!');</script>";
    } else {
        // Prepared statement to check if user/email exists
        $stmt_cek = mysqli_prepare($conn, "SELECT * FROM users WHERE nama=? OR email=?");
        mysqli_stmt_bind_param($stmt_cek, "ss", $nama, $email);
        mysqli_stmt_execute($stmt_cek);
        $res_cek = mysqli_stmt_get_result($stmt_cek);
        $data_user = mysqli_fetch_assoc($res_cek);
        mysqli_stmt_close($stmt_cek);

        if ($data_user) {
            echo "<script>alert('Nama atau email sudah terdaftar!');</script>";
        } else {
            // Hash the password using BCRYPT
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'mahasiswa';

            // Prepared statement to insert new user
            $stmt_insert = mysqli_prepare($conn, "INSERT INTO users (nama, email, role, password) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt_insert, "ssss", $nama, $email, $role, $hashed_password);
            
            if (mysqli_stmt_execute($stmt_insert)) {
                $_SESSION['login'] = true;
                $_SESSION['id_user'] = mysqli_insert_id($conn);
                $_SESSION['nama'] = $nama;
                $_SESSION['role'] = $role;
                mysqli_stmt_close($stmt_insert);

                header("Location: home.php");
                exit;
            } else {
                mysqli_stmt_close($stmt_insert);
                echo "<script>alert('Pendaftaran gagal! Silakan coba lagi.');</script>";
            }
        }
    }
}
?>

<link rel="stylesheet" href="style.css">

<div class="login-body">
    <div class="login-box">
        <h2>Daftar Akun</h2>

        <form method="POST">
            <label>Nama</label>
            <input type="text" name="nama" placeholder="Masukkan nama" required>

            <label>Email</label>
            <input type="email" name="email" placeholder="Masukkan email" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan password" required>

            <button type="submit" name="register">Daftar</button>
        </form>

        <p class="login-link">Sudah punya akun? <a href="login.php">Login</a></p>
    </div>
</div>

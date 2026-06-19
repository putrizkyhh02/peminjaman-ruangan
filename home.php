<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

include 'config/koneksi.php';

$role = $_SESSION['role'];
$user_id = (int) $_SESSION['id_user'];

if ($role === 'admin') {
    // Admin sees system-wide statistics
    $jumlah_peminjaman = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM peminjaman"))['total'];
    $jumlah_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"))['total'];
    $jumlah_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM peminjaman WHERE status='pending'"))['total'];
    $jumlah_disetujui = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM peminjaman WHERE status='disetujui'"))['total'];
} else {
    // Mahasiswa sees their own statistics
    $jumlah_peminjaman = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM peminjaman WHERE id_user=$user_id"))['total'];
    $jumlah_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM peminjaman WHERE id_user=$user_id AND status='pending'"))['total'];
    $jumlah_disetujui = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM peminjaman WHERE id_user=$user_id AND status='disetujui'"))['total'];
}
?>

<link rel="stylesheet" href="style.css?v=20260619-home-dashboard">
<body class="home-page">

<div class="navbar">
    <div class="nav-left">
        <a href="home.php" class="active">Home</a>
    </div>

    <div class="nav-right">
        <a class="nav-icon" href="peminjaman.php">Peminjaman</a>
        <?php if ($role === 'admin') { ?>
            <a class="nav-icon" href="index.php">Users</a>
            <a class="nav-icon" href="ruangan.php">Ruangan</a>
        <?php } ?>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="home-panel home-campus-hero">
    <div class="home-hero-content">
        <div class="home-heading">
            <img class="home-logo" src="assets/logo-polinela.png" alt="Logo Polinela">
            <div>
                <p>Selamat datang, <b><?= htmlspecialchars($_SESSION['nama']); ?></b></p>
                <h2>Pinjam ruangan kampus jadi lebih mudah.</h2>
                <span class="home-subtitle">Ajukan ruang kelas atau laboratorium, pantau statusnya, lalu cetak bukti peminjaman dalam satu tempat.</span>
            </div>
        </div>

        <div class="home-actions">
            <a class="btn-primary btn-large" href="tambah_peminjaman.php">+ Ajukan Peminjaman</a>
            <a class="btn-secondary" href="peminjaman.php">Lihat Semua Pengajuan</a>
        </div>
    </div>

    <div class="home-hero-photo" aria-hidden="true"></div>
</div>

<div class="summary-grid">
    <div class="summary-box">
        <div class="summary-icon">▣</div>
        <span><?= $role === 'admin' ? 'Total Peminjaman' : 'Peminjaman Saya'; ?></span>
        <strong><?= $jumlah_peminjaman; ?></strong>
        <small>Seluruh pengajuan tercatat</small>
    </div>

    <div class="summary-box summary-warning">
        <div class="summary-icon">◷</div>
        <span>Menunggu Approve</span>
        <strong><?= $jumlah_pending; ?></strong>
        <small>Menunggu keputusan admin</small>
    </div>

    <div class="summary-box summary-success">
        <div class="summary-icon">✓</div>
        <span>Disetujui</span>
        <strong><?= $jumlah_disetujui; ?></strong>
        <small>Siap digunakan sesuai jadwal</small>
    </div>

    <?php if ($role === 'admin') { ?>
        <div class="summary-box">
            <div class="summary-icon">♙</div>
            <span>Total Users</span>
            <strong><?= $jumlah_users; ?></strong>
            <small>Akun terdaftar di sistem</small>
        </div>
    <?php } ?>
</div>

<section class="home-guide">
    <div class="guide-heading">
        <div>
            <span>ALUR PEMINJAMAN</span>
            <h3>Tiga langkah, langsung beres.</h3>
        </div>
        <a href="tambah_peminjaman.php">Mulai sekarang →</a>
    </div>

    <div class="guide-grid">
        <article>
            <b>01</b>
            <div><h4>Isi pengajuan</h4><p>Masukkan nama, ruangan, jadwal, dan keperluan peminjaman.</p></div>
        </article>
        <article>
            <b>02</b>
            <div><h4>Pantau status</h4><p>Admin akan memeriksa dan memperbarui status pengajuan Anda.</p></div>
        </article>
        <article>
            <b>03</b>
            <div><h4>Cetak bukti</h4><p>Cetak laporan pribadi untuk ditunjukkan saat mengambil kunci.</p></div>
        </article>
    </div>
</section>
</body>

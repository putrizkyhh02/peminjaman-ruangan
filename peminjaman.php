<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

include 'config/koneksi.php';

$keyword = isset($_GET['q']) ? mysqli_real_escape_string($conn, trim($_GET['q'])) : "";
$filter_status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : "";

$where = [];

if ($keyword != "") {
    $where[] = "(COALESCE(NULLIF(p.nama_peminjam,''), u.nama) LIKE '%$keyword%' OR p.program_studi LIKE '%$keyword%' OR p.npm LIKE '%$keyword%' OR COALESCE(NULLIF(p.nama_ruangan,''), r.nama_ruangan) LIKE '%$keyword%' OR p.keperluan LIKE '%$keyword%')";
}

if (in_array($filter_status, ["pending", "disetujui", "approve"], true)) {
    $where[] = "p.status='$filter_status'";
}

$where_sql = "";
if (count($where) > 0) {
    $where_sql = "WHERE " . implode(" AND ", $where);
}
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

<div class="page-header">
    <div>
        <p>Welcome, <b><?= htmlspecialchars($_SESSION['nama']); ?></b> (Role: <?= htmlspecialchars($_SESSION['role']); ?>)</p>
        <h2>Data Peminjaman Ruangan</h2>
    </div>

    <div class="page-actions">
        <a class="btn-primary" href="tambah_peminjaman.php">+ Tambah Peminjaman</a>
    </div>
</div>

<form class="filter-box" method="GET">
    <input type="text" name="q" value="<?= htmlspecialchars($keyword); ?>" placeholder="Cari...">

    <select name="status">
        <option value="">Semua</option>
        <option value="pending" <?= $filter_status == 'pending' ? 'selected' : ''; ?>>Pending</option>
        <option value="disetujui" <?= $filter_status == 'disetujui' ? 'selected' : ''; ?>>Disetujui</option>
        <option value="approve" <?= $filter_status == 'approve' ? 'selected' : ''; ?>>Approve</option>
    </select>

    <button type="submit">Filter</button>
    <a class="btn-secondary" href="peminjaman.php">Reset</a>
</form>

<?php
$query = mysqli_query($conn, "
    SELECT
        p.id_pinjam,
        p.id_user,
        COALESCE(NULLIF(p.nama_peminjam, ''), u.nama) AS nama,
        p.program_studi,
        p.npm,
        COALESCE(NULLIF(p.nama_ruangan, ''), r.nama_ruangan) AS nama_ruangan,
        p.tanggal,
        p.jam_mulai,
        p.jam_selesai,
        p.keperluan,
        p.status
    FROM peminjaman p
    LEFT JOIN users u ON p.id_user = u.id_user
    LEFT JOIN ruangan r ON p.id_ruangan = r.id_ruangan
    $where_sql
    ORDER BY p.id_pinjam DESC
");
?>

<div class="table-wrap">
    <table>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Prodi / NPM</th>
            <th>Ruangan</th>
            <th>Tanggal</th>
            <th>Waktu</th>
            <th>Keperluan</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>

        <?php if (mysqli_num_rows($query) == 0) { ?>
        <tr>
            <td colspan="9">Tidak ada data</td>
        </tr>
        <?php } ?>

        <?php while($data = mysqli_fetch_assoc($query)) { ?>
        <tr>
            <td><?= $data['id_pinjam']; ?></td>
            <td><?= htmlspecialchars($data['nama']); ?></td>
            <td><?= htmlspecialchars($data['program_studi']); ?><br><small><?= htmlspecialchars($data['npm']); ?></small></td>
            <td><?= htmlspecialchars($data['nama_ruangan']); ?></td>
            <td><?= htmlspecialchars($data['tanggal']); ?></td>
            <td><?= substr($data['jam_mulai'],0,5); ?> - <?= substr($data['jam_selesai'],0,5); ?></td>
            <td><?= htmlspecialchars($data['keperluan']); ?></td>

            <td>
                <?php if ($_SESSION['role'] === 'admin') { ?>
                    <form class="status-control" method="POST" action="ubah_status.php">
                        <input type="hidden" name="id" value="<?= $data['id_pinjam']; ?>">
                        <label for="status-<?= $data['id_pinjam']; ?>">Status</label>
                        <select id="status-<?= $data['id_pinjam']; ?>" name="status" onchange="this.form.submit()">
                            <option value="pending" <?= $data['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="disetujui" <?= $data['status'] === 'disetujui' ? 'selected' : ''; ?>>Disetujui</option>
                            <option value="approve" <?= $data['status'] === 'approve' ? 'selected' : ''; ?>>Approve</option>
                        </select>
                    </form>
                <?php } else { ?>
                    <?php if($data['status'] === 'pending') { ?>
                        <span class="status-badge status-pending">Pending</span>
                    <?php } elseif($data['status'] === 'disetujui') { ?>
                        <span class="status-badge status-approved">Disetujui</span>
                    <?php } else { ?>
                        <span class="status-badge status-approved">Approve</span>
                    <?php } ?>
                <?php } ?>
            </td>

            <td>

                <?php if($_SESSION['role'] === 'admin' || $data['id_user'] == $_SESSION['id_user']) { ?>
                    <a class="btn-approve" href="edit_peminjaman.php?id=<?= $data['id_pinjam']; ?>">Edit</a>
                <?php } ?>

                <?php if($_SESSION['role'] === 'admin' || $data['id_user'] == $_SESSION['id_user']) { ?>
                    <a class="btn-delete" href="hapus_peminjaman.php?id=<?= $data['id_pinjam']; ?>" onclick="return confirm('Yakin ingin menghapus laporan ini?')">Hapus</a>
                <?php } ?>

                <?php if($data['id_user'] == $_SESSION['id_user']) { ?>
                    <a class="btn-print-row" href="cetak_peminjaman.php?id=<?= $data['id_pinjam']; ?>">Cetak Laporan Saya</a>
                <?php } ?>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

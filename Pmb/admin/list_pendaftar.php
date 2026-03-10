<?php
require_once '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Handle search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_safe = mysqli_real_escape_string($conn, $search);
$search_query = '';
if (!empty($search_safe)) {
    $search_query = "AND (nama_lengkap LIKE '%$search_safe%' OR email LIKE '%$search_safe%' OR nomor_test LIKE '%$search_safe%')";
}

$prodi_filter = isset($_GET['prodi']) ? trim($_GET['prodi']) : '';
$prodi_safe = mysqli_real_escape_string($conn, $prodi_filter);
$prodi_query = '';
if ($prodi_safe !== '') {
    $prodi_query = "AND jurusan_pilihan = '$prodi_safe'";
}

$mahasiswa = mysqli_query(
    $conn,
    "SELECT * FROM calon_mahasiswa 
     WHERE 1=1 $search_query $prodi_query
    ORDER BY created_at DESC"
);

$prodi_list = mysqli_query(
    $conn,
    "SELECT DISTINCT jurusan_pilihan FROM calon_mahasiswa WHERE jurusan_pilihan IS NOT NULL AND jurusan_pilihan != '' ORDER BY jurusan_pilihan"
);

$page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>List Pendaftaran</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f6fa;
            color: #333;
        }

        .wrapper {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, #1f2344 0%, #171b35 100%);
            color: #fff;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 22px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255,255,255,0.12);
            font-size: 18px;
        }

        .sidebar-header h2 {
            font-size: 18px;
            font-weight: 700;
            margin: 0;
        }

        .sidebar-header p {
            font-size: 11px;
            color: rgba(255,255,255,0.65);
            margin-top: 2px;
        }

        .sidebar-menu {
            padding: 14px 0;
            flex: 1;
        }

        .menu-section-title {
            padding: 10px 18px 6px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.5);
            font-weight: 600;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 10px 14px;
            margin: 6px 12px;
            color: rgba(255,255,255,0.82);
            text-decoration: none;
            transition: all 0.25s ease;
            border-radius: 10px;
        }

        .menu-item:hover {
            background: rgba(255,255,255,0.08);
            color: #fff;
        }

        .menu-item.active {
            background: rgba(122,162,255,0.22);
            color: #fff;
            box-shadow: inset 0 0 0 1px rgba(122,162,255,0.35);
        }

        .menu-item i {
            width: 20px;
            margin-right: 12px;
            font-size: 16px;
        }

        .logout-btn {
            margin: 12px 16px 18px;
            padding: 12px;
            background: rgba(255,255,255,0.08);
            color: #fff;
            text-align: center;
            text-decoration: none;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: 0.25s;
            border: 1px solid rgba(255,255,255,0.15);
        }

        .logout-btn:hover {
            background: rgba(255,255,255,0.16);
        }

        .main {
            margin-left: 250px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: calc(100% - 250px);
        }

        .topbar {
            background: #fff;
            padding: 16px 26px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eef0f2;
        }

        .topbar-title {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            font-weight: 600;
        }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-greet {
            font-size: 13px;
            color: #374151;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #1f2344;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 14px;
        }

        .content {
            padding: 26px;
            flex: 1;
            width: 100%;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 24px rgba(16,24,40,0.08);
            padding: 26px;
            margin-bottom: 22px;
            border: 1px solid #edf0f6;
            width: 100%;
        }

        .card h3 {
            margin-bottom: 18px;
            color: #0f172a;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
            font-size: 13px;
            border-radius: 12px;
            overflow: hidden;
        }

        th {
            background: #f1f4f9;
            padding: 12px;
            text-align: left;
            font-weight: 700;
            color: #334155;
            border-bottom: 1px solid #e6eaf2;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.6px;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eef0f2;
            vertical-align: top;
        }

        tr:hover {
            background: #f8f9fb;
        }

        .btn {
            padding: 8px 12px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-hapus {
            background: #ef4444;
            color: #fff;
        }

        .btn-hapus:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        .btn-edit {
            background: #f59e0b;
            color: #fff;
        }

        .btn-edit:hover {
            background: #d97706;
            transform: translateY(-1px);
        }

        .btn-primary {
            background: #1f2344;
            color: #fff;
            padding: 11px 18px;
            box-shadow: 0 8px 16px rgba(31,35,68,0.25);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            background: #181c34;
            box-shadow: 0 12px 20px rgba(31,35,68,0.35);
        }

        /* HEADER CARD */
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 20px;
        }

        .card-header h3 {
            background: #1f2344;
            color: #fff;
            padding: 10px 16px;
            border-radius: 8px;
            margin: 0;
            flex-shrink: 0;
        }

        .search-box {
            display: flex;
            gap: 10px;
            flex: 1;
        }

        .search-box input {
            flex: 1;
            padding: 12px 14px;
            border: 1px solid #d8dde6;
            border-radius: 8px;
            font-size: 14px;
            background: #fbfcff;
            transition: 0.2s;
        }

        .filter-select {
            min-width: 180px;
            padding: 12px 14px;
            border: 1px solid #d8dde6;
            border-radius: 8px;
            font-size: 14px;
            background: #fbfcff;
            transition: 0.2s;
        }

        .filter-select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.15);
        }

        .search-box input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.15);
        }

        .btn-search {
            background: #1f2344;
            color: #fff;
            padding: 11px 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn-search:hover {
            background: #2c3354;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
            border: 1px solid transparent;
        }

        .status-lulus {
            background: #e7f6ea;
            color: #1e7e34;
            border-color: #bfe5c7;
        }

        .status-tidak-lulus {
            background: #fdecea;
            color: #b42318;
            border-color: #f5c2c7;
        }

        .status-belum-test {
            background: #fff4cc;
            color: #8a6d1d;
            border-color: #f0e1a1;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: nowrap;
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <div class="wrapper">

        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="brand-icon"><img src="../assets/logo.png" alt="Logo PMB" style="width: 100%; height: 100%; object-fit: contain;"></div>
                <div>
                    <h2>PMB Office</h2>
                    <p>Admin Panel</p>
                </div>
            </div>

            <nav class="sidebar-menu">
                <div class="menu-section-title">Dashboards</div>
                <a class="menu-item" href="dashboard.php">
                    <i class="fa-solid fa-chart-line"></i> Dashboard
                </a>

                <div class="menu-section-title">Informasi Maba</div>
                <a class="menu-item" href="kelola_soal.php">
                    <i class="fa-solid fa-pen-to-square"></i> Kelola Soal Tes
                </a>
                <a class="menu-item active" href="list_pendaftar.php">
                    <i class="fa-solid fa-user-group"></i> List Pendaftaran
                </a>
                <a class="menu-item" href="hasil_test.php">
                    <i class="fa-solid fa-chart-pie"></i> Hasil Tes
                </a>
                <a class="menu-item" href="daftar_ulang.php">
                    <i class="fa-solid fa-id-card"></i> Data Daftar Ulang
                </a>
            </nav>

            <a href="logout.php" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </aside>

        <main class="main">

            <header class="topbar">
                <div class="topbar-title">Sistem Penerimaan Mahasiswa Baru</div>
                <div class="topbar-user">
                    <div class="user-greet">Hi, <strong><?= $_SESSION['admin_name'] ?? 'Admin'; ?></strong></div>
                    <div class="user-avatar"><?= strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)) ?></div>
                </div>
            </header>

            <section class="content">
                <div class="container">
                    <div class="card">
                    <div class="card-header">
                        <h3><i class="fa-solid fa-users"></i> Data Pendaftaran</h3>
                        
                        <form method="GET" class="search-box">
                            <input type="text" name="search" placeholder="Cari nama, email, atau nomor tes..." 
                                   value="<?= htmlspecialchars($search) ?>">
                            <select name="prodi" class="filter-select">
                                <option value="">Semua Prodi</option>
                                <?php while ($prodi = mysqli_fetch_assoc($prodi_list)): ?>
                                    <option value="<?= htmlspecialchars($prodi['jurusan_pilihan']) ?>" <?= $prodi_filter === $prodi['jurusan_pilihan'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($prodi['jurusan_pilihan']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <button type="submit" class="btn-search">
                                <i class="fa-solid fa-search"></i> Cari
                            </button>
                        </form>

                        <a href="tambah_pendaftar.php" class="btn btn-primary">
                            <i class="fa-solid fa-plus"></i> Tambah Pendaftar
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table>
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>No. HP</th>
                                <th>Asal Sekolah</th>
                                <th>Prodi</th>
                                <th>Tanggal Daftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            $has_data = false;
                            while ($row = mysqli_fetch_assoc($mahasiswa)): 
                                $has_data = true;
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $row['nama_lengkap'] ?></td>
                                    <td><?= $row['email'] ?></td>
                                    <td><?= $row['no_hp'] ?></td>
                                    <td><?= $row['asal_sekolah'] ?></td>
                                    <td><?= $row['jurusan_pilihan'] ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="edit_pendaftar.php?id=<?= $row['id_calon']; ?>" class="btn btn-edit">
                                                <i class="fa-solid fa-edit"></i> Edit
                                            </a>
                                            <a href="hapus_hasil_test.php?id=<?= $row['id_calon']; ?>&from=list_pendaftar.php" class="btn btn-hapus"
                                                onclick="return confirm('Yakin ingin menghapus data ini?')">
                                                <i class="fa-solid fa-trash"></i> Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            <?php if (!$has_data): ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 40px; color: #6c757d;">
                                        <i class="fa-solid fa-inbox" style="font-size: 48px; opacity: 0.3; display: block; margin-bottom: 10px;"></i>
                                        <?php if (!empty($search)): ?>
                                            <strong>Tidak ada hasil untuk "<?= htmlspecialchars($search) ?>"</strong><br>
                                            <a href="list_pendaftar.php" style="color: #1f2344; margin-top: 10px; display: inline-block;">Tampilkan semua data</a>
                                        <?php else: ?>
                                            <strong>Belum ada data pendaftar</strong>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    </div>

                    </div>
                </div>
            </section>

        </main>
    </div>
</body>

</html>
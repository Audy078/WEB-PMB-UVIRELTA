<?php
require_once '../config.php';

// Stats Queries
$total_pendaftar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM calon_mahasiswa"))['total'];
$total_lulus = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM calon_mahasiswa WHERE status_test='lulus'"))['total'];
$total_daftar_ulang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM calon_mahasiswa WHERE status_daftar_ulang='sudah'"))['total'];
$total_soal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM soal"))['total'];
$belum_test = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM calon_mahasiswa WHERE status_test='belum_test'"))['total'];
$belum_verifikasi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM calon_mahasiswa WHERE (tempat_lahir IS NULL OR tempat_lahir = '') OR (tanggal_lahir IS NULL OR tanggal_lahir = '') OR (jenis_kelamin IS NULL OR jenis_kelamin = '')"))['total'];

// Data per prodi
$prodi_data = mysqli_query($conn, "SELECT jurusan_pilihan, COUNT(*) as total FROM calon_mahasiswa WHERE jurusan_pilihan IS NOT NULL AND jurusan_pilihan != '' GROUP BY jurusan_pilihan ORDER BY total DESC");

$page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin PMB</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                }

                body {
                    background: #f5f6fa;
                    color: #1c1e21;
                }

                .wrapper {
                    display: flex;
                    min-height: 100vh;
                }

                /* ===== SIDEBAR ===== */
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

                /* ===== MAIN CONTENT ===== */
                .main {
                    flex: 1;
                    margin-left: 250px;
                    display: flex;
                    flex-direction: column;
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

                .user-greet {
                    font-size: 13px;
                    color: #374151;
                }

                /* ===== CONTENT ===== */
                .content {
                    padding: 26px;
                    flex: 1;
                }

                .welcome-section {
                    margin-bottom: 26px;
                }

                .welcome-title {
                    font-size: 26px;
                    font-weight: 700;
                    color: #111827;
                    margin-bottom: 6px;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }

                .welcome-subtitle {
                    color: #6b7280;
                    font-size: 14px;
                }

                .alert {
                    padding: 14px 18px;
                    border-radius: 10px;
                    margin-bottom: 18px;
                    font-weight: 500;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }

                .alert-success {
                    background: #d4edda;
                    color: #155724;
                    border-left: 4px solid #28a745;
                }

                .alert-error {
                    background: #f8d7da;
                    color: #721c24;
                    border-left: 4px solid #dc3545;
                }

                /* ===== STAT CARDS ===== */
                .stats-grid {
                    display: grid;
                    grid-template-columns: repeat(4, minmax(200px, 1fr));
                    gap: 18px;
                    margin-bottom: 24px;
                }

                .stat-card {
                    background: #fff;
                    padding: 22px;
                    border-radius: 14px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
                    display: flex;
                    align-items: center;
                    gap: 18px;
                    transition: transform 0.3s, box-shadow 0.3s;
                }

                .stat-card:hover {
                    transform: translateY(-3px);
                    box-shadow: 0 4px 14px rgba(0,0,0,0.12);
                }

                .stat-icon {
                    width: 56px;
                    height: 56px;
                    border-radius: 12px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 22px;
                    color: #fff;
                }

                .stat-icon.blue {
                    background: #667eea;
                }

                .stat-icon.green {
                    background: #28a745;
                }

                .stat-icon.orange {
                    background: #fd7e14;
                }

                .stat-icon.red {
                    background: #dc3545;
                }

                .stat-icon.purple {
                    background: #6f42c1;
                }

                .stat-info h3 {
                    font-size: 28px;
                    font-weight: 700;
                    color: #111827;
                    margin-bottom: 4px;
                }

                .stat-info p {
                    font-size: 13px;
                    color: #6b7280;
                    font-weight: 500;
                }

                /* ===== GRID LAYOUT ===== */
                .grid-2 {
                    display: grid;
                    grid-template-columns: 1fr 1.15fr;
                    gap: 18px;
                }

                .card {
                    background: #fff;
                    padding: 24px;
                    border-radius: 14px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
                }

                .card h3 {
                    font-size: 17px;
                    font-weight: 700;
                    color: #111827;
                    margin-bottom: 18px;
                    padding-bottom: 12px;
                    border-bottom: 1px solid #eef0f2;
                }

                /* ===== FORM STYLES ===== */
                .form-group {
                    margin-bottom: 14px;
                }

                .form-group label {
                    display: block;
                    margin-bottom: 6px;
                    font-weight: 600;
                    font-size: 13px;
                    color: #374151;
                }

                .form-row {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 12px;
                }

                input,
                textarea,
                select {
                    width: 100%;
                    padding: 11px 12px;
                    border-radius: 8px;
                    border: 1px solid #d8dde6;
                    font-size: 14px;
                    transition: border-color 0.2s, box-shadow 0.2s;
                }

                input:focus,
                textarea:focus,
                select:focus {
                    outline: none;
                    border-color: #667eea;
                    box-shadow: 0 0 0 3px rgba(102,126,234,0.15);
                }

                button {
                    margin-top: 12px;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    padding: 12px 18px;
                    border: none;
                    border-radius: 8px;
                    cursor: pointer;
                    font-weight: 600;
                    width: 100%;
                    font-size: 14px;
                    transition: transform 0.2s, box-shadow 0.2s;
                }

                button:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(102,126,234,0.35);
                }

                /* ===== TABLE ===== */
                .table-wrapper {
                    overflow-x: auto;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 13px;
                }

                table th,
                table td {
                    border-bottom: 1px solid #eef0f2;
                    padding: 12px 8px;
                    text-align: left;
                }

                table th {
                    background: #f8f9fb;
                    font-weight: 600;
                    color: #4b5563;
                    text-transform: uppercase;
                    font-size: 11px;
                    letter-spacing: 0.5px;
                }

                table tr:hover {
                    background: #f8f9fb;
                }

                .status-badge {
                    display: inline-block;
                    padding: 4px 10px;
                    border-radius: 20px;
                    font-size: 11px;
                    font-weight: 600;
                    text-transform: capitalize;
                }

                .badge-lulus {
                    background: #d4edda;
                    color: #155724;
                }

                .badge-tidak-lulus {
                    background: #f8d7da;
                    color: #721c24;
                }

                .badge-belum {
                    background: #fff3cd;
                    color: #856404;
                }

                .view-all {
                    margin-top: 12px;
                    text-align: center;
                }

                .view-all a {
                    color: #5b8dee;
                    font-weight: 600;
                    text-decoration: none;
                }

                /* ===== CHART SECTION ===== */
                .dashboard-section {
                    margin-bottom: 24px;
                }

                .chart-card {
                    background: #fff;
                    padding: 24px;
                    border-radius: 12px;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                }

                .chart-card h3 {
                    font-size: 16px;
                    margin-bottom: 20px;
                    color: #1f2937;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }

                .chart-bar {
                    margin-bottom: 16px;
                }

                .chart-bar-header {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 6px;
                    font-size: 13px;
                }

                .chart-bar-label {
                    font-weight: 600;
                    color: #374151;
                }

                .chart-bar-value {
                    font-weight: 700;
                    color: #1f2344;
                }

                .chart-bar-fill {
                    height: 32px;
                    background: #f3f4f6;
                    border-radius: 6px;
                    overflow: hidden;
                    position: relative;
                }

                .chart-bar-progress {
                    height: 100%;
                    background: linear-gradient(135deg, #1f2344 0%, #4a5677 100%);
                    border-radius: 6px;
                    transition: width 1s ease;
                    display: flex;
                    align-items: center;
                    justify-content: flex-end;
                    padding-right: 10px;
                    color: #fff;
                    font-size: 12px;
                    font-weight: 600;
                }

                @media (max-width: 1200px) {
                    .stats-grid {
                        grid-template-columns: repeat(2, minmax(220px, 1fr));
                    }
                }

                @media (max-width: 1100px) {
                    .grid-2 {
                        grid-template-columns: 1fr;
                    }
                }

                @media (max-width: 900px) {
                    .sidebar {
                        position: static;
                        width: 100%;
                        height: auto;
                    }

                    .main {
                        margin-left: 0;
                    }

                    .wrapper {
                        flex-direction: column;
                    }
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
                <a class="menu-item <?= $page == 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">
                    <i class="fa-solid fa-chart-line"></i> Dashboard
                </a>

                <div class="menu-section-title">Informasi Maba</div>
                <a class="menu-item <?= $page == 'kelola_soal.php' ? 'active' : '' ?>" href="kelola_soal.php">
                    <i class="fa-solid fa-pen-to-square"></i> Kelola Soal Tes
                </a>
                <a class="menu-item <?= $page == 'list_pendaftar.php' ? 'active' : '' ?>" href="list_pendaftar.php">
                    <i class="fa-solid fa-user-group"></i> List Pendaftaran
                </a>
                <a class="menu-item <?= $page == 'hasil_test.php' ? 'active' : '' ?>" href="hasil_test.php">
                    <i class="fa-solid fa-chart-pie"></i> Hasil Tes
                </a>
                <a class="menu-item <?= $page == 'daftar_ulang.php' ? 'active' : '' ?>" href="daftar_ulang.php">
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
                <div class="welcome-section">
                    <div class="welcome-title">Selamat Datang, <?= $_SESSION['admin_name'] ?? 'Admin'; ?></div>
                    <div class="welcome-subtitle">Ringkasan data pendaftar dan status seleksi.</div>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon blue"><i class="fa-solid fa-user-plus"></i></div>
                        <div class="stat-info">
                            <h3><?= $total_pendaftar ?></h3>
                            <p>Pendaftar</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon green"><i class="fa-solid fa-circle-check"></i></div>
                        <div class="stat-info">
                            <h3><?= $total_lulus ?></h3>
                            <p>Lulus Tes</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon orange"><i class="fa-solid fa-user-check"></i></div>
                        <div class="stat-info">
                            <h3><?= $total_daftar_ulang ?></h3>
                            <p>Daftar Ulang</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon purple"><i class="fa-solid fa-file-lines"></i></div>
                        <div class="stat-info">
                            <h3><?= $total_soal ?></h3>
                            <p>Total Soal</p>
                        </div>
                    </div>
                </div>

                <!-- GRAFIK & NOTIFIKASI -->
                <div class="dashboard-section">
                    <!-- GRAFIK PENDAFTAR PER PRODI -->
                    <div class="chart-card">
                        <h3><i class="fa-solid fa-chart-bar"></i> Jumlah pendaftar per prodi</h3>
                        <?php 
                        $max_value = 0;
                        $prodi_array = [];
                        while($row = mysqli_fetch_assoc($prodi_data)) {
                            $prodi_array[] = $row;
                            if($row['total'] > $max_value) $max_value = $row['total'];
                        }
                        
                        if(empty($prodi_array)): ?>
                            <p style="text-align: center; color: #6c757d; padding: 40px 0;">
                                <i class="fa-solid fa-inbox" style="font-size: 48px; opacity: 0.3; display: block; margin-bottom: 10px;"></i>
                                Belum ada data pendaftar
                            </p>
                        <?php else:
                            foreach($prodi_array as $prodi): 
                                $percentage = $max_value > 0 ? ($prodi['total'] / $max_value) * 100 : 0;
                        ?>
                            <div class="chart-bar">
                                <div class="chart-bar-header">
                                    <span class="chart-bar-label"><?= htmlspecialchars($prodi['jurusan_pilihan']) ?></span>
                                    <span class="chart-bar-value"><?= $prodi['total'] ?> Pendaftar</span>
                                </div>
                                <div class="chart-bar-fill">
                                    <div class="chart-bar-progress" style="width: <?= $percentage ?>%">
                                        <?= number_format($percentage, 1) ?>%
                                    </div>
                                </div>
                            </div>
                        <?php 
                            endforeach;
                        endif; 
                        ?>
                    </div>

                </div>

            </section>
        </main>
    </div>
</body>

</html>
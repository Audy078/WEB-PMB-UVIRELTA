<?php
require_once '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_safe = mysqli_real_escape_string($conn, $search);
$search_query = '';
if ($search_safe !== '') {
    $search_query = "AND (nama_lengkap LIKE '%$search_safe%' OR nim LIKE '%$search_safe%' OR nomor_test LIKE '%$search_safe%' OR jurusan_pilihan LIKE '%$search_safe%')";
}

$prodi_filter = isset($_GET['prodi']) ? trim($_GET['prodi']) : '';
$prodi_safe = mysqli_real_escape_string($conn, $prodi_filter);
$prodi_query = '';
if ($prodi_safe !== '') {
    $prodi_query = "AND jurusan_pilihan = '$prodi_safe'";
}

$mahasiswa = mysqli_query(
    $conn,
    "SELECT * FROM calon_mahasiswa WHERE status_daftar_ulang = 'sudah' $search_query $prodi_query ORDER BY created_at DESC"
);

$prodi_list = mysqli_query(
    $conn,
    "SELECT DISTINCT jurusan_pilihan FROM calon_mahasiswa WHERE status_daftar_ulang = 'sudah' AND jurusan_pilihan IS NOT NULL AND jurusan_pilihan != '' ORDER BY jurusan_pilihan"
);
$page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Daftar Ulang</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif
        }

        body {
            background: #f4f6fb
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
            flex: 1;
            padding: 26px;
            overflow-y: auto;
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
            font-size: 16px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
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
            display: inline-flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .btn-search:hover {
            background: #2c3354;
        }

        .table-responsive {
            overflow-x: auto;
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
            font-size: 13px;
        }

        tr:hover {
            background: #f8f9fb;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
            white-space: nowrap;
        }

        .badge-success i {
            font-size: 10px;
            margin-right: 4px;
        }

        .btn-detail {
            background: #1f2344;
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-detail:hover {
            background: #2b2f55;
        }

        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.45);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 999;
            padding: 20px;
        }

        .modal.show {
            display: flex;
        }

        .modal-content {
            background: #fff;
            width: 100%;
            max-width: 520px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }

        .modal-header {
            padding: 16px 20px;
            background: #1f2344;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-header h4 {
            font-size: 16px;
            margin: 0;
        }

        .modal-close {
            background: transparent;
            border: none;
            color: #fff;
            font-size: 18px;
            cursor: pointer;
        }

        .modal-body {
            padding: 18px 20px;
        }

        .modal-row {
            padding: 10px 0;
            border-bottom: 1px solid #eef0f2;
        }

        .modal-row:last-child {
            border-bottom: none;
        }

        .modal-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .modal-value {
            font-size: 14px;
            color: #111827;
            font-weight: 600;
            word-break: break-word;
        }

        @media (max-width: 900px) {
            .sidebar {
                position: static;
                width: 100%;
                height: auto;
            }

            .main {
                margin-left: 0;
                width: 100%;
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
                <a class="menu-item" href="dashboard.php">
                    <i class="fa-solid fa-chart-line"></i> Dashboard
                </a>

                <div class="menu-section-title">Informasi Maba</div>
                <a class="menu-item" href="kelola_soal.php">
                    <i class="fa-solid fa-pen-to-square"></i> Kelola Soal Tes
                </a>
                <a class="menu-item" href="list_pendaftar.php">
                    <i class="fa-solid fa-user-group"></i> List Pendaftaran
                </a>
                <a class="menu-item" href="hasil_test.php">
                    <i class="fa-solid fa-chart-pie"></i> Hasil Tes
                </a>
                <a class="menu-item active" href="daftar_ulang.php">
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
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fa-solid fa-user-check"></i> Data Daftar Ulang</h3>
                        <form method="GET" class="search-box">
                            <input
                                type="text"
                                name="search"
                                placeholder="Cari nama, NIM, nomor tes, atau prodi..."
                                value="<?= htmlspecialchars($search) ?>"
                            >
                            <select name="prodi" class="filter-select">
                                <option value="">Semua Prodi</option>
                                <?php while ($prodi = mysqli_fetch_assoc($prodi_list)): ?>
                                    <option value="<?= htmlspecialchars($prodi['jurusan_pilihan']) ?>" <?= $prodi_filter === $prodi['jurusan_pilihan'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($prodi['jurusan_pilihan']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <button type="submit" class="btn-search">
                                <i class="fa-solid fa-magnifying-glass"></i> Cari
                            </button>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table>
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>NIM</th>
                                <th>Nama Mahasiswa</th>
                                <th>Prodi</th>
                                <th>Nomor HP</th>
                                <th>Tanggal Konfirmasi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            if (mysqli_num_rows($mahasiswa) > 0):
                                while ($row = mysqli_fetch_assoc($mahasiswa)): 
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><b><?= htmlspecialchars($row['nim'] ?: '-') ?></b></td>
                                    <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                    <td><?= htmlspecialchars($row['jurusan_pilihan']) ?></td>
                                    <td><?= htmlspecialchars($row['no_hp'] ?? '-') ?></td>
                                    <td><?= !empty($row['updated_at']) ? date('d/m/Y H:i', strtotime($row['updated_at'])) : '-' ?></td>
                                    <td><span class="badge-success"><i class="fa-solid fa-check"></i> Sudah Daftar Ulang</span></td>
                                    <td>
                                        <button
                                            type="button"
                                            class="btn-detail"
                                            data-nama="<?= htmlspecialchars($row['nama_lengkap']) ?>"
                                            data-nomor="<?= htmlspecialchars($row['nomor_test']) ?>"
                                            data-prodi="<?= htmlspecialchars($row['jurusan_pilihan']) ?>"
                                            data-hp-ortu="<?= htmlspecialchars($row['nomor_orang_tua'] ?? '-') ?>"
                                        >
                                            <i class="fa-solid fa-circle-info"></i> Detail
                                        </button>
                                    </td>
                                </tr>
                            <?php 
                                endwhile;
                            else:
                            ?>
                                <tr>
                                    <td colspan="9" style="text-align: center; padding: 40px; color: #6c757d;">
                                        <i class="fa-solid fa-inbox" style="font-size: 48px; opacity: 0.3; display: block; margin-bottom: 10px;"></i>
                                        <strong>Belum ada mahasiswa yang daftar ulang</strong>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        </table>
                    </div>

                </div>
            </section>

        </main>
    </div>

    <div class="modal" id="detailModal" aria-hidden="true">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Info Mahasiswa</h4>
                <button class="modal-close" type="button" id="closeModal"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <div class="modal-row">
                    <div class="modal-label">Nama Lengkap</div>
                    <div class="modal-value" id="detailNama">-</div>
                </div>
                <div class="modal-row">
                    <div class="modal-label">Nomor Tes</div>
                    <div class="modal-value" id="detailNomor">-</div>
                </div>
                <div class="modal-row">
                    <div class="modal-label">Program Studi</div>
                    <div class="modal-value" id="detailProdi">-</div>
                </div>
                <div class="modal-row">
                    <div class="modal-label">No. HP Orang Tua/Wali</div>
                    <div class="modal-value" id="detailHpOrtu">-</div>
                </div>
                
            </div>
        </div>
    </div>

    <script>
        const modal = document.getElementById('detailModal');
        const closeModal = document.getElementById('closeModal');
        const detailNama = document.getElementById('detailNama');
        const detailNomor = document.getElementById('detailNomor');
        const detailProdi = document.getElementById('detailProdi');
        const detailHpOrtu = document.getElementById('detailHpOrtu');

        document.querySelectorAll('.btn-detail').forEach((btn) => {
            btn.addEventListener('click', () => {
                detailNama.textContent = btn.dataset.nama || '-';
                detailNomor.textContent = btn.dataset.nomor || '-';
                detailProdi.textContent = btn.dataset.prodi || '-';
                detailHpOrtu.textContent = btn.dataset.hpOrtu || '-';
                modal.classList.add('show');
                modal.setAttribute('aria-hidden', 'false');
            });
        });

        const closeDetailModal = () => {
            modal.classList.remove('show');
            modal.setAttribute('aria-hidden', 'true');
        };

        closeModal.addEventListener('click', closeDetailModal);
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeDetailModal();
            }
        });
    </script>
</body>

</html>
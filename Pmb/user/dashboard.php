<?php
require_once '../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$user = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT * FROM calon_mahasiswa WHERE id_calon = '$user_id'")
);

$foto = (!empty($user['foto']) && file_exists('../uploads/' . $user['foto']))
    ? '../uploads/' . $user['foto']
    : '../assets/image/default-user.png';

$nama_user = $user['nama_lengkap'] ?? 'User';

// Cek biodata lengkap
$required_fields = ['tempat_lahir', 'tanggal_lahir', 'jenis_kelamin'];
$biodata_lengkap = true;
foreach($required_fields as $field) {
    if (empty($user[$field])) {
        $biodata_lengkap = false;
        break;
    }
}

// Determine step status
$steps = [
    1 => ['title' => 'Mengisi Biodata', 'desc' => 'Lengkapi semua data pribadi Anda.', 'status' => ($biodata_lengkap ? 'selesai' : 'pending'), 'link' => 'profil.php'],
    2 => ['title' => 'Lakukan Tes Masuk', 'desc' => 'Waktu pengerjaan 30 menit.', 'status' => ($user['status_test']!='belum_test'?'selesai':'pending'), 'link' => 'test.php', 'disabled' => !$biodata_lengkap],
    3 => ['title' => 'Daftar Ulang', 'desc' => 'Konfirmasi daftar ulang dan cetak dokumen di menu Cetak Dokumen.', 'status' => ($user['status_daftar_ulang']=='sudah'?'selesai':'pending'), 'link' => 'daftar_ulang.php', 'disabled' => ($user['status_test']!='lulus')]
];

$program_studi = $user['jurusan_pilihan'] ?: 'Belum dipilih';
$nomor_test = $user['nomor_test'] ?: '-';

$status_pendaftaran = 'Belum Tes';
$status_class = 'warning';
if ($user['status_daftar_ulang'] === 'sudah') {
    $status_pendaftaran = 'Sudah Daftar Ulang';
    $status_class = 'success';
} elseif ($user['status_test'] === 'lulus') {
    $status_pendaftaran = 'Lulus Tes';
    $status_class = 'success';
} elseif ($user['status_test'] === 'tidak_lulus') {
    $status_pendaftaran = 'Tidak Lulus';
    $status_class = 'danger';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Calon Mahasiswa</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', sans-serif;
            background: #f5f6f8;
            color: #333;
            line-height: 1.6;
        }

        /* ======= TOP BAR ======= */
        .topbar {
            background: #1f2344;
            color: #fff;
            padding: 16px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 650;
            font-size: 16px;
            letter-spacing: -0.3px;
        }

        .topbar-logo {
            width: 34px;
            height: 34px;
            object-fit: contain;
            display: block;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-right .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255,255,255,0.8);
        }

        .profile-badge {
            background: rgba(255,255,255,0.15);
            color: #fff;
            padding: 7px 14px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            border: 1px solid rgba(255,255,255,0.2);
        }

        /* ======= CONTAINER ======= */
        .container {
            display: flex;
            min-height: calc(100vh - 70px);
            background: #f5f6f8;
        }

        /* ======= SIDEBAR ======= */
        .sidebar {
            width: 260px;
            background: #fff;
            padding: 25px 0;
            border-right: 1px solid #e8e8e8;
            box-shadow: 2px 0 8px rgba(0,0,0,0.04);
            overflow-y: auto;
            position: sticky;
            top: 0;
            height: 100vh;
        }

        .user-info {
            padding: 0 20px 25px;
            border-bottom: 1px solid #e8e8e8;
            margin-bottom: 25px;
        }

        .user-info-avatar {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            object-fit: cover;
            margin-bottom: 10px;
            border: 2px solid #e8e8e8;
        }

        .user-info h4 {
            font-size: 15px;
            margin-bottom: 2px;
            color: #1b2a6d;
            font-weight: 700;
        }

        .user-info p {
            font-size: 12px;
            color: #999;
            font-weight: 500;
        }

        .nav-items {
            display: flex;
            flex-direction: column;
        }

        .nav-item {
            padding: 10px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #666;
            text-decoration: none;
            transition: all 0.25s ease;
            cursor: pointer;
            font-size: 13px;
            border-left: 3px solid transparent;
            font-weight: 500;
        }

        .nav-item:hover {
            background: #f8f9fa;
            color: #1b2a6d;
            border-left-color: #1b2a6d;
        }

        .nav-item.active {
            background: #e8f0fd;
            color: #1b2a6d;
            border-left-color: #1b2a6d;
            font-weight: 600;
        }

        .nav-item i {
            font-size: 15px;
            width: 16px;
        }

        .nav-item.logout {
            color: #e74c3c;
            margin-top: auto;
            border-top: 1px solid #e8e8e8;
            padding-top: 15px;
            margin-top: 15px;
        }

        .nav-item.logout:hover {
            background: #fee;
        }

        .nav-item.disabled {
            opacity: 0.7;
            pointer-events: none;
            cursor: not-allowed;
            color: #9e9e9e !important;
        }

        .nav-item.disabled:hover {
            background: #f3f4f6;
            border-left-color: #bdbdbd;
        }

        /* ======= MAIN CONTENT ======= */
        .main-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        /* ======= GREETING SECTION ======= */
        .greeting-section {
            background: #fff;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid #f0f0f0;
        }

        .greeting-text {
            flex: 1;
        }

        .greeting-text h2 {
            font-size: 28px;
            color: #1b2a6d;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .greeting-text p {
            color: #777;
            font-size: 14px;
            line-height: 1.6;
        }

        .greeting-illustration {
            font-size: 90px;
            color: #90caf9;
            opacity: 0.85;
            margin-left: 30px;
            flex-shrink: 0;
        }

        .greeting-summary {
            margin-top: 28px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .summary-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .summary-icon {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #c5dff8;
            color: #1f2344;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            flex-shrink: 0;
        }

        .summary-label {
            display: block;
            font-size: 13px;
            color: #6b7280;
            line-height: 1.2;
        }

        .summary-value {
            display: block;
            margin-top: 2px;
            font-size: 14px;
            color: #1f2937;
            line-height: 1.35;
            font-weight: 600;
        }

        .summary-value.status.success {
            color: #2e7d32;
        }

        .summary-value.status.warning {
            color: #e65100;
        }

        .summary-value.status.danger {
            color: #c62828;
        }

        /* ======= STEPS SECTION ======= */
        .steps-section {
            background: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid #f0f0f0;
        }

        .steps-title {
            font-size: 16px;
            font-weight: 700;
            color: #1b2a6d;
            margin-bottom: 25px;
        }

        .step-item {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #f0f0f0;
        }

        .step-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .step-number {
            min-width: 36px;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            color: #fff;
            flex-shrink: 0;
            font-family: 'Segoe UI', sans-serif;
        }

        .step-number.completed {
            background: #4caf50;
        }

        .step-number.pending {
            background: #ff9800;
        }

        .step-number.completed i {
            font-size: 16px;
        }

        .step-content {
            flex: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 3px;
        }

        .step-info h4 {
            font-size: 15px;
            font-weight: 700;
            color: #1b2a6d;
            margin-bottom: 4px;
        }

        .step-info p {
            font-size: 13px;
            color: #888;
        }

        .step-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
            text-transform: capitalize;
        }

        .step-badge.completed {
            background: #c8e6c9;
            color: #2e7d32;
        }

        .step-badge.pending {
            background: #ffe0b2;
            color: #e65100;
        }

        .step-badge.locked {
            background: #ffcdd2;
            color: #c62828;
        }

        /* ======= RESPONSIVE ======= */
        @media (max-width: 1200px) {
            .greeting-summary {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .greeting-illustration {
                font-size: 60px;
                margin-left: 20px;
            }
        }

        @media (max-width: 992px) {
            .container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                border-right: none;
                border-bottom: 1px solid #e8e8e8;
                padding: 20px;
                display: flex;
                min-height: auto;
                gap: 30px;
            }

            .user-info {
                border-bottom: none;
                margin-bottom: 0;
                padding: 0;
                border-right: 1px solid #e8e8e8;
                padding-right: 30px;
                min-width: 200px;
            }

            .nav-items {
                flex-direction: row;
                gap: 5px;
                flex: 1;
            }

            .nav-item {
                padding: 8px 0;
                border-left: none;
                border-bottom: 2px solid transparent;
            }

            .nav-item.active {
                border-left: none;
                border-bottom-color: #1b2a6d;
                background: transparent;
            }

            .nav-item.logout {
                border-top: none;
                border-left: 1px solid #e8e8e8;
                padding-left: 20px;
                margin-top: 0;
                padding-top: 8px;
            }

            .greeting-section {
                flex-direction: column;
                text-align: center;
            }

            .greeting-text {
                max-width: 100%;
            }

            .greeting-illustration {
                margin-left: 0;
                margin-top: 15px;
            }

            .greeting-summary {
                width: 100%;
                text-align: left;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                flex-direction: column;
                gap: 15px;
            }

            .user-info {
                border-right: none;
                border-bottom: 1px solid #e8e8e8;
                padding-right: 0;
                margin-bottom: 15px;
                padding-bottom: 15px;
                min-width: 100%;
            }

            .nav-items {
                flex-direction: row;
                flex-wrap: wrap;
            }

            .nav-item.logout {
                border-left: none;
                padding-left: 20px;
            }

            .greeting-text h2 {
                font-size: 24px;
            }

            .main-content {
                padding: 20px;
            }

            .greeting-illustration {
                font-size: 50px;
            }

            .step-content {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .step-badge {
                align-self: flex-start;
            }
        }

        @media (max-width: 600px) {
            .topbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
                padding: 12px 20px;
            }

            .topbar-right {
                width: 100%;
                justify-content: space-between;
            }

            .sidebar {
                flex-direction: column;
                padding: 15px;
            }

            .nav-items {
                flex-direction: column;
            }

            .nav-item {
                border-left: 3px solid transparent;
            }

            .nav-item.logout {
                border-left: 3px solid transparent;
                padding-left: 20px;
            }

            .greeting-section {
                padding: 20px;
            }

            .greeting-text h2 {
                font-size: 20px;
            }

            .greeting-text p {
                font-size: 13px;
            }

            .greeting-summary {
                grid-template-columns: 1fr;
                gap: 14px;
            }

            .summary-value {
                font-size: 13px;
            }

            .steps-section {
                padding: 20px;
            }

            .step-item {
                gap: 12px;
            }

            .step-number {
                width: 42px;
                height: 42px;
                font-size: 14px;
            }

            .step-info h4 {
                font-size: 14px;
            }

            .step-info p {
                font-size: 12px;
            }
        }
    </style>
</head>

<body>

<!-- TOP BAR -->
<div class="topbar">
    <div class="topbar-left">
        <img src="../assets/logo.png" alt="Logo" class="topbar-logo">
        <div>
            Portal Pendaftaran Camaba<br>
            <small>Universitas Virelta Indonesia</small>
        </div> 
    </div>
    <div class="topbar-right">
        <img src="<?= $foto ?>" class="avatar">
        <div class="profile-badge">
            <i class="fas fa-user-circle"></i>
            <span><?= htmlspecialchars($nama_user); ?></span>
        </div>
    </div>
</div>

<!-- CONTAINER -->
<div class="container">
    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="user-info">
            <img src="<?= $foto ?>" class="user-info-avatar">
            <h4><?= htmlspecialchars($nama_user); ?></h4>
            <p>Camaba 2026/2027</p>
        </div>

        <div class="nav-items">
            <a href="dashboard.php" class="nav-item active">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="profil.php" class="nav-item">
                <i class="fas fa-user"></i>
                <span>Biodata</span>
            </a>
            <?php 
            $test_disabled = !$biodata_lengkap || $user['status_test'] != 'belum_test';
            ?>
            <a href="<?= $test_disabled ? '#' : 'test.php' ?>" class="nav-item <?= $test_disabled ? 'disabled' : '' ?>" <?= $test_disabled ? 'aria-disabled="true"' : '' ?>>
                <i class="fas fa-file-pen"></i>
                <span>Ikuti Tes</span>
            </a>
            <a href="hasil_test.php" class="nav-item">
                <i class="fas fa-chart-line"></i>
                <span>Hasil Tes</span>
            </a>
            <a href="daftar_ulang.php" class="nav-item <?= ($user['status_test']!='lulus')?'disabled':'' ?>">
                <i class="fas fa-user-graduate"></i>
                <span>Daftar Ulang</span>
            </a>
            <a href="kartu_mahasiswa.php" class="nav-item <?= ($user['status_daftar_ulang']!='sudah')?'disabled':'' ?>">
                <i class="fas fa-print"></i>
                <span>Cetak Dokumen</span>
            </a>
            <a href="logout.php" class="nav-item logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <!-- GREETING SECTION -->
        <div class="greeting-section">
            <div class="greeting-text">
                <h2>Hai, <?= htmlspecialchars($nama_user); ?></h2>
                <p>Selamat datang di portal pendaftaran mahasiswa baru. Berikut adalah ringkasan status pendaftaran dan progres Anda.</p>
                <div class="greeting-summary">
                    <div class="summary-item">
                        <span class="summary-icon"><i class="fas fa-book-open"></i></span>
                        <div>
                            <span class="summary-label">Program Studi</span>
                            <span class="summary-value"><?= htmlspecialchars($program_studi) ?></span>
                        </div>
                    </div>
                    <div class="summary-item">
                        <span class="summary-icon"><i class="fas fa-id-card"></i></span>
                        <div>
                            <span class="summary-label">Nomor Tes</span>
                            <span class="summary-value"><?= htmlspecialchars($nomor_test) ?></span>
                        </div>
                    </div>
                    <div class="summary-item">
                        <span class="summary-icon"><i class="fas fa-clipboard-check"></i></span>
                        <div>
                            <span class="summary-label">Status Pendaftaran</span>
                            <span class="summary-value status <?= $status_class ?>"><?= htmlspecialchars($status_pendaftaran) ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="greeting-illustration">
                <i class="fas fa-graduation-cap"></i>
            </div>
        </div>

        <!-- STEPS SECTION -->
        <div class="steps-section">
            <h3 class="steps-title">Tahapan Pendaftaran</h3>
            
            <?php foreach($steps as $index => $step): ?>
                <div class="step-item">
                    <div class="step-number <?= $step['status'] == 'selesai' ? 'completed' : 'pending' ?>">
                        <?php if($step['status'] == 'selesai'): ?>
                            <i class="fas fa-check"></i>
                        <?php else: ?>
                            <?= $index ?>
                        <?php endif; ?>
                    </div>
                    <div class="step-content">
                        <div class="step-info">
                            <h4><?= $step['title'] ?></h4>
                            <p><?= $step['desc'] ?></p>
                        </div>
                        <?php if($step['status'] == 'selesai'): ?>
                            <div class="step-badge completed">Selesai</div>
                        <?php elseif(isset($step['disabled']) && $step['disabled']): ?>
                            <div class="step-badge locked">Terkunci</div>
                        <?php else: ?>
                            <a href="<?= $step['link'] ?>" class="step-badge pending" style="text-decoration: none;">Lanjutkan</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

</body>
</html>

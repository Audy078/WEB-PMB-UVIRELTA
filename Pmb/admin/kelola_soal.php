<?php
include '../config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$message = '';
$error = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    $query = "DELETE FROM soal WHERE id_soal = '$id'";
    if (mysqli_query($conn, $query)) {
        $message = "Soal berhasil dihapus!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id_soal']) ? mysqli_real_escape_string($conn, $_POST['id_soal']) : '';
    $pertanyaan = mysqli_real_escape_string($conn, $_POST['pertanyaan']);
    $pilihan_a = mysqli_real_escape_string($conn, $_POST['pilihan_a']);
    $pilihan_b = mysqli_real_escape_string($conn, $_POST['pilihan_b']);
    $pilihan_c = mysqli_real_escape_string($conn, $_POST['pilihan_c']);
    $pilihan_d = mysqli_real_escape_string($conn, $_POST['pilihan_d']);
    $jawaban_benar = mysqli_real_escape_string($conn, $_POST['jawaban_benar']);

    if (empty($pertanyaan) || empty($pilihan_a) || empty($pilihan_b) || empty($pilihan_c) || empty($pilihan_d) || empty($jawaban_benar)) {
        $error = "Semua field harus diisi!";
    } else {
        if ($id) {
            // Update
            $query = "UPDATE soal SET pertanyaan='$pertanyaan', pilihan_a='$pilihan_a', pilihan_b='$pilihan_b', pilihan_c='$pilihan_c', pilihan_d='$pilihan_d', jawaban_benar='$jawaban_benar' WHERE id_soal='$id'";
            if (mysqli_query($conn, $query)) {
                $message = "Soal berhasil diubah!";
                $_POST = array();
                $id = '';
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        } else {
            // Add New
            $query = "INSERT INTO soal (pertanyaan, pilihan_a, pilihan_b, pilihan_c, pilihan_d, jawaban_benar) VALUES ('$pertanyaan', '$pilihan_a', '$pilihan_b', '$pilihan_c', '$pilihan_d', '$jawaban_benar')";
            if (mysqli_query($conn, $query)) {
                $message = "Soal baru berhasil ditambahkan!";
                $_POST = array();
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        }
    }
}

// Get soal for update
$edit_soal = null;
if (isset($_GET['edit'])) {
    $id = mysqli_real_escape_string($conn, $_GET['edit']);
    $query = "SELECT * FROM soal WHERE id_soal = '$id'";
    $result = mysqli_query($conn, $query);
    $edit_soal = mysqli_fetch_assoc($result);
}
// Get all soal
$query = "SELECT * FROM soal ORDER BY id_soal DESC";
$result = mysqli_query($conn, $query);
$soal_list = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Soal - Admin PMB</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
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
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .alert {
            padding: 14px 18px;
            margin-bottom: 18px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 6px 16px rgba(0,0,0,0.06);
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 24px rgba(16,24,40,0.08);
            padding: 26px;
            margin-bottom: 22px;
            border: 1px solid #edf0f6;
        }

        .card h2 {
            margin-bottom: 18px;
            color: #0f172a;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: #555;
        }

        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #d8dde6;
            border-radius: 10px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            background: #fbfcff;
        }

        textarea {
            resize: vertical;
            min-height: 90px;
        }

        input[type="text"]:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.15);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 18px;
        }

        button {
            padding: 11px 18px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: #1f2344;
            color: white;
            box-shadow: 0 8px 16px rgba(31,35,68,0.25);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            background: #181c34;
            box-shadow: 0 12px 20px rgba(31,35,68,0.35);
        }

        .btn-secondary {
            background: #eef2f7;
            color: #334155;
            border: 1px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
        }

        .btn-action {
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
            white-space: nowrap;
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
        }

        tr:hover {
            background: #f8f9fb;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .soal-number {
            font-weight: 600;
            color: #667eea;
            min-width: 30px;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: nowrap;
            white-space: nowrap;
        }

        .soal-info {
            font-size: 13px;
            color: #6b7280;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: #1f2344;
            color: white;
            padding: 24px 22px;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 12px 24px rgba(31,35,68,0.25);
        }

        .stat-card .number {
            font-size: 32px;
            font-weight: 700;
            margin: 8px 0;
        }

        .stat-card .label {
            font-size: 13px;
            opacity: 0.9;
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
                <a class="menu-item" href="dashboard.php">
                    <i class="fa-solid fa-chart-line"></i> Dashboard
                </a>

                <div class="menu-section-title">Informasi Maba</div>
                <a class="menu-item active" href="kelola_soal.php">
                    <i class="fa-solid fa-pen-to-square"></i> Kelola Soal Tes
                </a>
                <a class="menu-item" href="list_pendaftar.php">
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
        <!-- Stats -->
        <div class="stats">
            <div class="stat-card">
                <div class="label">Total Soal</div>
                <div class="number"><?php echo count($soal_list); ?></div>
            </div>
        </div>

        <!-- Messages -->
        <?php if ($message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Form Tambah/Edit Soal -->
        <div class="card">
            <h2>
                <i class="fas fa-<?php echo $edit_soal ? 'edit' : 'plus-circle'; ?>"></i>
                <?php echo $edit_soal ? 'Edit Soal' : 'Tambah Soal Baru'; ?>
            </h2>
            
            <form method="POST">
                <?php if ($edit_soal): ?>
                    <input type="hidden" name="id_soal" value="<?php echo htmlspecialchars($edit_soal['id_soal']); ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="pertanyaan">Pertanyaan <span style="color: red;">*</span></label>
                    <textarea name="pertanyaan" id="pertanyaan" required><?php echo $edit_soal ? htmlspecialchars($edit_soal['pertanyaan']) : ''; ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="pilihan_a">Pilihan A <span style="color: red;">*</span></label>
                        <input type="text" name="pilihan_a" id="pilihan_a" value="<?php echo $edit_soal ? htmlspecialchars($edit_soal['pilihan_a']) : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="pilihan_b">Pilihan B <span style="color: red;">*</span></label>
                        <input type="text" name="pilihan_b" id="pilihan_b" value="<?php echo $edit_soal ? htmlspecialchars($edit_soal['pilihan_b']) : ''; ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="pilihan_c">Pilihan C <span style="color: red;">*</span></label>
                        <input type="text" name="pilihan_c" id="pilihan_c" value="<?php echo $edit_soal ? htmlspecialchars($edit_soal['pilihan_c']) : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="pilihan_d">Pilihan D <span style="color: red;">*</span></label>
                        <input type="text" name="pilihan_d" id="pilihan_d" value="<?php echo $edit_soal ? htmlspecialchars($edit_soal['pilihan_d']) : ''; ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="jawaban_benar">Jawaban Benar <span style="color: red;">*</span></label>
                    <select name="jawaban_benar" id="jawaban_benar" required>
                        <option value="">-- Pilih Jawaban Benar --</option>
                        <option value="A" <?php echo $edit_soal && $edit_soal['jawaban_benar'] == 'A' ? 'selected' : ''; ?>>A</option>
                        <option value="B" <?php echo $edit_soal && $edit_soal['jawaban_benar'] == 'B' ? 'selected' : ''; ?>>B</option>
                        <option value="C" <?php echo $edit_soal && $edit_soal['jawaban_benar'] == 'C' ? 'selected' : ''; ?>>C</option>
                        <option value="D" <?php echo $edit_soal && $edit_soal['jawaban_benar'] == 'D' ? 'selected' : ''; ?>>D</option>
                    </select>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-<?php echo $edit_soal ? 'save' : 'plus'; ?>"></i>
                        <?php echo $edit_soal ? 'Update Soal' : 'Tambah Soal'; ?>
                    </button>
                    <?php if ($edit_soal): ?>
                        <a href="kelola_soal.php" style="text-decoration: none;">
                            <button type="button" class="btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </button>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- List Soal -->
        <div class="card">
            <h2><i class="fas fa-list"></i> Daftar Soal (<?php echo count($soal_list); ?>)</h2>
            
            <?php if (empty($soal_list)): ?>
                <div class="no-data">
                    <i class="fas fa-inbox" style="font-size: 48px; color: #ccc; margin-bottom: 10px;"></i>
                    <p>Belum ada soal. Silakan tambah soal baru!</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th style="width: 45%;">Pertanyaan</th>
                                <th style="width: 15%;">Jawaban</th>
                                <th style="width: 35%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($soal_list as $index => $soal): ?>
                                <tr>
                                    <td class="soal-number"><?php echo count($soal_list) - $index; ?></td>
                                    <td>
                                        <div style="font-weight: 500; margin-bottom: 5px;">
                                            <?php echo htmlspecialchars(substr($soal['pertanyaan'], 0, 60)) . (strlen($soal['pertanyaan']) > 60 ? '...' : ''); ?>
                                        </div>
                                        <div class="soal-info">
                                            A: <?php echo htmlspecialchars(substr($soal['pilihan_a'], 0, 30)); ?> | 
                                            B: <?php echo htmlspecialchars(substr($soal['pilihan_b'], 0, 30)); ?> | 
                                            C: <?php echo htmlspecialchars(substr($soal['pilihan_c'], 0, 30)); ?> | 
                                            D: <?php echo htmlspecialchars(substr($soal['pilihan_d'], 0, 30)); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span style="background: #57e41a; color: white; padding: 4px 8px; border-radius: 3px; font-weight: 600;">
                                            <?php echo htmlspecialchars($soal['jawaban_benar']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="?edit=<?php echo htmlspecialchars($soal['id_soal']); ?>" class="btn-action btn-edit">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="?delete=<?php echo htmlspecialchars($soal['id_soal']); ?>" class="btn-action btn-hapus" onclick="return confirm('Yakin ingin menghapus soal ini?')">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
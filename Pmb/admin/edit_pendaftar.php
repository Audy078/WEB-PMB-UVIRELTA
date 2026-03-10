<?php
require_once '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$success = '';
$error = '';

// Get student ID
if (!isset($_GET['id'])) {
    header('Location: list_pendaftar.php');
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Get student data
$result = mysqli_query($conn, "SELECT * FROM calon_mahasiswa WHERE id_calon = '$id'");
$student = mysqli_fetch_assoc($result);

if (!$student) {
    header('Location: list_pendaftar.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $asal_sekolah = mysqli_real_escape_string($conn, $_POST['asal_sekolah']);
    $jurusan = mysqli_real_escape_string($conn, $_POST['jurusan']);
    
    // Check if email already exists for other students
    $check = mysqli_query($conn, "SELECT * FROM calon_mahasiswa WHERE email = '$email' AND id_calon != '$id'");
    if (mysqli_num_rows($check) > 0) {
        $error = 'Email sudah digunakan oleh pendaftar lain!';
    } else {
        $query = "UPDATE calon_mahasiswa SET 
            nama_lengkap = '$nama',
            email = '$email',
            no_hp = '$no_hp',
            alamat = '$alamat',
            asal_sekolah = '$asal_sekolah',
            jurusan_pilihan = '$jurusan'
        WHERE id_calon = '$id'";
        
        // Update password if provided
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $query = "UPDATE calon_mahasiswa SET 
                nama_lengkap = '$nama',
                email = '$email',
                password = '$password',
                no_hp = '$no_hp',
                alamat = '$alamat',
                asal_sekolah = '$asal_sekolah',
                jurusan_pilihan = '$jurusan'
            WHERE id_calon = '$id'";
        }
        
        if (mysqli_query($conn, $query)) {
            $success = 'Data pendaftar berhasil diupdate!';
            // Refresh student data
            $result = mysqli_query($conn, "SELECT * FROM calon_mahasiswa WHERE id_calon = '$id'");
            $student = mysqli_fetch_assoc($result);
        } else {
            $error = 'Gagal mengupdate data: ' . mysqli_error($conn);
        }
    }
}

$page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Pendaftar</title>
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
            padding: 24px;
            border-radius: 12px;
            max-width: 800px;
        }

        .card h3 {
            color: #1f2344;
            margin-bottom: 8px;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-box {
            background: #e3f2fd;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #1565c0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: 0.2s;
            font-family: 'Segoe UI', sans-serif;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #1f2344;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .form-group input:disabled {
            background: #f5f5f5;
            cursor: not-allowed;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-primary {
            background: #1f2344;
            color: #fff;
        }

        .btn-primary:hover {
            background: #2c3354;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #6c757d;
            color: #fff;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        .required {
            color: #dc3545;
        }

        .help-text {
            font-size: 12px;
            color: #6c757d;
            margin-top: 4px;
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
                <div class="card">
                    <h3><i class="fa-solid fa-user-edit"></i> Edit Data Pendaftar</h3>
                    
                    <div class="info-box">
                        <i class="fa-solid fa-id-badge"></i>
                        <strong>Nomor Tes:</strong> <?= htmlspecialchars($student['nomor_test']) ?>
                    </div>

                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="fa-solid fa-check-circle"></i>
                            <?= $success ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fa-solid fa-exclamation-circle"></i>
                            <?= $error ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Nama Lengkap <span class="required">*</span></label>
                                <input type="text" name="nama" value="<?= htmlspecialchars($student['nama_lengkap']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Email <span class="required">*</span></label>
                                <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Password Baru</label>
                                <input type="password" name="password">
                                <div class="help-text">Kosongkan jika tidak ingin mengubah password</div>
                            </div>
                            <div class="form-group">
                                <label>No. HP <span class="required">*</span></label>
                                <input type="text" name="no_hp" value="<?= htmlspecialchars($student['no_hp']) ?>" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Asal Sekolah <span class="required">*</span></label>
                                <input type="text" name="asal_sekolah" value="<?= htmlspecialchars($student['asal_sekolah']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Prodi <span class="required">*</span></label>
                                <select name="jurusan" required>
                                    <option value="">Pilih Prodi...</option>
                                    <option value="Teknik Informatika" <?= $student['jurusan_pilihan'] == 'Teknik Informatika' ? 'selected' : '' ?>>Teknik Informatika</option>
                                    <option value="Sistem Informasi" <?= $student['jurusan_pilihan'] == 'Sistem Informasi' ? 'selected' : '' ?>>Sistem Informasi</option>
                                    <option value="Manajemen" <?= $student['jurusan_pilihan'] == 'Manajemen' ? 'selected' : '' ?>>Manajemen</option>
                                    <option value="Akuntansi" <?= $student['jurusan_pilihan'] == 'Akuntansi' ? 'selected' : '' ?>>Akuntansi</option>
                                    <option value="Hukum" <?= $student['jurusan_pilihan'] == 'Hukum' ? 'selected' : '' ?>>Hukum</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Alamat Lengkap <span class="required">*</span></label>
                            <textarea name="alamat" required><?= htmlspecialchars($student['alamat']) ?></textarea>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-save"></i> Update Data
                            </button>
                            <a href="list_pendaftar.php" class="btn btn-secondary">
                                <i class="fa-solid fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </section>
        </main>
    </div>
</body>
</html>

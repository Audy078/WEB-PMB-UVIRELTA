<?php
require_once '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password_plain = $_POST['password'];
    $password = password_hash($password_plain, PASSWORD_DEFAULT);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $asal_sekolah = mysqli_real_escape_string($conn, $_POST['asal_sekolah']);
    $jurusan = mysqli_real_escape_string($conn, $_POST['jurusan']);
    
    // Check if email already exists
    $check = mysqli_query($conn, "SELECT * FROM calon_mahasiswa WHERE email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = 'Email sudah terdaftar!';
    } else {
        // Generate nomor test
        $last_nomor = mysqli_query($conn, "SELECT nomor_test FROM calon_mahasiswa ORDER BY id_calon DESC LIMIT 1");
        $last = mysqli_fetch_assoc($last_nomor);
        
        if ($last && $last['nomor_test']) {
            $num = intval(substr($last['nomor_test'], 3)) + 1;
        } else {
            $num = 1;
        }
        $nomor_test = 'PMB' . str_pad($num, 4, '0', STR_PAD_LEFT);
        
        $query = "INSERT INTO calon_mahasiswa (
            nama_lengkap, email, password, no_hp, alamat, asal_sekolah, jurusan_pilihan, 
            nomor_test, status_test, created_at
        ) VALUES (
            '$nama', '$email', '$password', '$no_hp', '$alamat', '$asal_sekolah', '$jurusan',
            '$nomor_test', 'belum_test', NOW()
        )";
        
        if (mysqli_query($conn, $query)) {
            $success = 'Pendaftar berhasil ditambahkan dengan nomor tes: ' . $nomor_test;
        } else {
            $error = 'Gagal menambahkan data: ' . mysqli_error($conn);
        }
    }
}

$page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Pendaftar</title>
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
            margin-bottom: 20px;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
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
                    <h3><i class="fa-solid fa-user-plus"></i> Tambah Pendaftar Baru</h3>

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
                                <input type="text" name="nama" required>
                            </div>
                            <div class="form-group">
                                <label>Email <span class="required">*</span></label>
                                <input type="email" name="email" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Password <span class="required">*</span></label>
                                <input type="password" name="password" required>
                            </div>
                            <div class="form-group">
                                <label>No. HP <span class="required">*</span></label>
                                <input type="text" name="no_hp" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Asal Sekolah <span class="required">*</span></label>
                                <input type="text" name="asal_sekolah" required>
                            </div>
                            <div class="form-group">
                                <label>Prodi Pilihan <span class="required">*</span></label>
                                <select name="jurusan" required>
                                    <option value="">Pilih Prodi...</option>
                                    <option value="Teknik Informatika">Teknik Informatika</option>
                                    <option value="Sistem Informasi">Sistem Informasi</option>
                                    <option value="Manajemen">Manajemen</option>
                                    <option value="Akuntansi">Akuntansi</option>
                                    <option value="Hukum">Hukum</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Alamat Lengkap <span class="required">*</span></label>
                            <textarea name="alamat" required></textarea>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-save"></i> Simpan Data
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

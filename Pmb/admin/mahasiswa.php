<?php
require_once '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['delete'])) {
    $id = clean($_GET['delete']);
    mysqli_query($conn, "DELETE FROM calon_mahasiswa WHERE id_calon = '$id'");
    header('Location: mahasiswa.php?msg=deleted');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id_calon']) ? clean($_POST['id_calon']) : '';
    $nama = clean($_POST['nama_lengkap']);
    $email = clean($_POST['email']);
    $no_hp = clean($_POST['no_hp']);
    $alamat = clean($_POST['alamat']);
    $asal_sekolah = clean($_POST['asal_sekolah']);
    $jurusan = clean($_POST['jurusan_pilihan']);

    if ($id) {
        $query = "UPDATE calon_mahasiswa SET 
                  nama_lengkap='$nama', email='$email', no_hp='$no_hp',
                  alamat='$alamat', asal_sekolah='$asal_sekolah', jurusan_pilihan='$jurusan'
                  WHERE id_calon='$id'";
    } else {
        // Insert
        $password = 'password123'; // Default password
        $query = "INSERT INTO calon_mahasiswa (nama_lengkap, email, password, no_hp, alamat, asal_sekolah, jurusan_pilihan) 
                  VALUES ('$nama', '$email', '$password', '$no_hp', '$alamat', '$asal_sekolah', '$jurusan')";
    }

    mysqli_query($conn, $query);
    header('Location: mahasiswa.php?msg=success');
    exit();
}

// Get data for edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = clean($_GET['edit']);
    $result = mysqli_query($conn, "SELECT * FROM calon_mahasiswa WHERE id_calon = '$id'");
    $edit_data = mysqli_fetch_assoc($result);
}

// Get all mahasiswa
$mahasiswa = mysqli_query($conn, "SELECT * FROM calon_mahasiswa ORDER BY id_calon DESC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Mahasiswa - Admin PMB</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1em;
        }

        .form-group textarea {
            min-height: 100px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1em;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
        }

        .btn-success {
            background: #43e97b;
            color: white;
        }

        .btn-danger {
            background: #fa709a;
            color: white;
        }

        .btn-warning {
            background: #fee140;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        th {
            background: #f8f9fa;
            font-weight: 600;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.85em;
            display: inline-block;
        }

        .badge-primary {
            background: #e3f2fd;
            color: #1976d2;
        }

        .badge-success {
            background: #e8f5e9;
            color: #388e3c;
        }

        .badge-danger {
            background: #ffebee;
            color: #d32f2f;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <h1>👨‍🎓 Kelola Calon Mahasiswa</h1>
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
            <div class="alert alert-success">✓ Data berhasil disimpan!</div>
        <?php endif; ?>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
            <div class="alert alert-success">✓ Data berhasil dihapus!</div>
        <?php endif; ?>

        <div class="card">
            <h2 style="margin-bottom: 25px;">
                <?php echo $edit_data ? '✏️ Edit Data' : '➕ Tambah Data Baru'; ?>
            </h2>

            <form method="POST">
                <?php if ($edit_data): ?>
                    <input type="hidden" name="id_calon" value="<?php echo $edit_data['id_calon']; ?>">
                <?php endif; ?>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Nama Lengkap *</label>
                        <input type="text" name="nama_lengkap" required
                            value="<?php echo $edit_data ? $edit_data['nama_lengkap'] : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" required
                            value="<?php echo $edit_data ? $edit_data['email'] : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>No. HP *</label>
                        <input type="text" name="no_hp" required
                            value="<?php echo $edit_data ? $edit_data['no_hp'] : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>Asal Sekolah *</label>
                        <input type="text" name="asal_sekolah" required
                            value="<?php echo $edit_data ? $edit_data['asal_sekolah'] : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>Jurusan Pilihan *</label>
                        <select name="jurusan_pilihan" required>
                            <option value="">-- Pilih Jurusan --</option>
                            <option value="Teknik Informatika" <?php echo ($edit_data && $edit_data['jurusan_pilihan'] == 'Teknik Informatika') ? 'selected' : ''; ?>>Teknik
                                Informatika</option>
                            <option value="Sistem Informasi" <?php echo ($edit_data && $edit_data['jurusan_pilihan'] == 'Sistem Informasi') ? 'selected' : ''; ?>>Sistem
                                Informasi</option>
                            <option value="Manajemen" <?php echo ($edit_data && $edit_data['jurusan_pilihan'] == 'Manajemen') ? 'selected' : ''; ?>>Manajemen</option>
                            <option value="Akuntansi" <?php echo ($edit_data && $edit_data['jurusan_pilihan'] == 'Akuntansi') ? 'selected' : ''; ?>>Akuntansi</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Alamat *</label>
                    <textarea name="alamat" required><?php echo $edit_data ? $edit_data['alamat'] : ''; ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <?php echo $edit_data ? '💾 Update Data' : '➕ Tambah Data'; ?>
                </button>
                <?php if ($edit_data): ?>
                    <a href="mahasiswa.php" class="btn btn-warning">✖ Batal</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="card">
            <h2 style="margin-bottom: 20px;">📋 Data Calon Mahasiswa</h2>

            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>No. HP</th>
                        <th>Jurusan</th>
                        <th>Status Tes</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($mahasiswa)):
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $row['nama_lengkap']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['no_hp']; ?></td>
                            <td><?php echo $row['jurusan_pilihan']; ?></td>
                            <td>
                                <?php if ($row['status_test'] == 'lulus'): ?>
                                    <span class="badge badge-success">Lulus</span>
                                <?php elseif ($row['status_test'] == 'tidak_lulus'): ?>
                                    <span class="badge badge-danger">Tidak Lulus</span>
                                <?php else: ?>
                                    <span class="badge badge-primary">Belum Tes</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="?edit=<?php echo $row['id_calon']; ?>" class="btn btn-warning"
                                    style="font-size: 0.9em; padding: 6px 12px;">Edit</a>
                                <a href="?delete=<?php echo $row['id_calon']; ?>"
                                    onclick="return confirm('Yakin ingin menghapus?')" class="btn btn-danger"
                                    style="font-size: 0.9em; padding: 6px 12px;">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
<?php
require_once '../config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$query_user = mysqli_query($conn, "SELECT * FROM calon_mahasiswa WHERE id_calon='$user_id'");
$user = mysqli_fetch_assoc($query_user);

if (!$user) {
    echo "<script>alert('Data user tidak ditemukan!'); window.location='logout.php';</script>";
    exit();
}
if (isset($_POST['update_data'])) {
    if (empty($user['foto'])) {
        $_SESSION['alert'] = 'Upload foto profil dulu agar bisa lanjut.';
        header('Location: profil.php');
        exit();
    }
    $nik = mysqli_real_escape_string($conn, $_POST['nik']);
    $tempat_lahir = mysqli_real_escape_string($conn, $_POST['tempat_lahir']);
    $tanggal_lahir = mysqli_real_escape_string($conn, $_POST['tanggal_lahir']);
    $jenis_kelamin = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);

    // Validasi
    if (empty($nik) || empty($tempat_lahir) || empty($tanggal_lahir) || empty($jenis_kelamin)) {
        echo "<script>alert('Semua field harus diisi!');</script>";
    } else {
        $sql = "UPDATE calon_mahasiswa SET 
                nik='$nik',
                tempat_lahir='$tempat_lahir',
                tanggal_lahir='$tanggal_lahir',
                jenis_kelamin='$jenis_kelamin'
                WHERE id_calon='$user_id'";
        
        if(mysqli_query($conn, $sql)) {
            echo "<script>alert('Data berhasil diperbarui!'); window.location='dashboard.php';</script>";
            exit();
        } else {
            echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
        }
    }
}

if (isset($_POST['update_foto'])) {
    if (!empty($_FILES['foto']['name'])) {
        // Cek file error
        if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            $error_msg = "Upload error!";
            switch($_FILES['foto']['error']) {
                case UPLOAD_ERR_NO_FILE: $error_msg = "File tidak dipilih"; break;
                case UPLOAD_ERR_INI_SIZE: $error_msg = "File terlalu besar"; break;
                case UPLOAD_ERR_FORM_SIZE: $error_msg = "File terlalu besar"; break;
            }
            echo "<script>alert('$error_msg');</script>";
        } else {
            // Cek & hapus foto lama
            if (!empty($user['foto']) && file_exists('../uploads/' . $user['foto'])) {
                unlink('../uploads/' . $user['foto']);
            }

            $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png'];
            $max_size = 5 * 1024 * 1024; // 5MB

            // Validasi extension
            if (!in_array($ext, $allowed)) {
                echo "<script>alert('Format file tidak valid! Gunakan JPG, JPEG, atau PNG.');</script>";
            } 
            // Validasi size
            elseif ($_FILES['foto']['size'] > $max_size) {
                echo "<script>alert('Ukuran file tidak boleh lebih dari 5MB!');</script>";
            } 
            else {
                // Cek folder uploads
                if (!is_dir('../uploads')) {
                    mkdir('../uploads', 0755, true);
                }

                $namaFoto = 'foto_' . $user_id . '_' . time() . '.' . $ext;
                $path = '../uploads/' . $namaFoto;

                // Upload file
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $path)) {
                    // Update database
                    $namaFoto_escaped = mysqli_real_escape_string($conn, $namaFoto);
                    $query = "UPDATE calon_mahasiswa SET foto='$namaFoto_escaped' WHERE id_calon='$user_id'";
                    
                    if (mysqli_query($conn, $query)) {
                        echo "<script>alert('Foto berhasil diupload!'); window.location='profil.php';</script>";
                        exit();
                    } else {
                        // Hapus file jika query gagal
                        unlink($path);
                        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
                    }
                } else {
                    echo "<script>alert('Gagal mengupload file. Cek permission folder uploads.');</script>";
                }
            }
        }
    }
}

// =======================
// HAPUS FOTO
// =======================
if (isset($_POST['hapus_foto'])) {
    if (!empty($user['foto'])) {
        $foto_path = '../uploads/' . $user['foto'];
        
        // Hapus file
        if (file_exists($foto_path)) {
            if (unlink($foto_path)) {
                // Update database
                $query = "UPDATE calon_mahasiswa SET foto=NULL WHERE id_calon='$user_id'";
                if (mysqli_query($conn, $query)) {
                    echo "<script>alert('Foto berhasil dihapus!'); window.location='profil.php';</script>";
                    exit();
                } else {
                    echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
                }
            } else {
                echo "<script>alert('Gagal menghapus file foto!');</script>";
            }
        } else {
            // File tidak ada di folder, hapus dari database saja
            $query = "UPDATE calon_mahasiswa SET foto=NULL WHERE id_calon='$user_id'";
            if (mysqli_query($conn, $query)) {
                echo "<script>alert('Data foto berhasil dihapus!'); window.location='profil.php';</script>";
                exit();
            } else {
                echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
            }
        }
    }
}

// Path foto
$foto_default = 'https://ui-avatars.com/api/?name=' . urlencode($user['nama_lengkap']) . '&size=150&background=4267B2&color=fff';
if (!empty($user['foto'])) {
    $foto_path = '../uploads/' . $user['foto'];
    $foto = file_exists($foto_path) ? $foto_path : $foto_default;
} else {
    $foto = $foto_default;
}
$foto_required = empty($user['foto']);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Data Diri</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            color: #1c1e21;
        }

        /* NAVBAR */
        .navbar {
            background: #1f2344;
            color: #fff;
            padding: 12px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar h1 {
            font-size: 18px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-dashboard, .btn-back {
            background: rgba(255,255,255,0.2);
            color: #fff;
            padding: 8px 20px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .btn-dashboard:hover, .btn-back:hover {
            background: rgba(255,255,255,0.3);
        }

        /* CONTAINER */
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 20px;
            margin-bottom: 20px;
            align-items: start;
        }

        /* CARD */
        .card {
            background: #fff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .card h2 {
            font-size: 20px;
            margin-bottom: 20px;
            color: #050505;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card h2 i {
            color: #4267B2;
        }

        /* FOTO SECTION */
        .foto-section {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .foto-section img {
            width: 150px;
            height: 150px;
            border-radius: 8px;
            object-fit: cover;
            margin-bottom: 15px;
            border: 3px solid #4267B2;
        }

        .foto-section p {
            font-size: 13px;
            color: #65676b;
            margin-bottom: 15px;
        }

        /* FORM */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-group label {
            font-size: 14px;
            font-weight: 600;
            color: #050505;
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            font-family: 'Segoe UI', sans-serif;
            transition: .2s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #4267B2;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .form-group input:disabled {
            background: #f5f5f5;
            cursor: not-allowed;
        }

        /* BUTTONS */
        .btn-group {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 18px;
            border: 1px solid transparent;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color .2s ease, border-color .2s ease, transform .1s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            letter-spacing: 0.2px;
        }

        .btn-primary {
            background: #1f5cc5;
            border-color: #1f5cc5;
            color: #fff;
        }

        .btn-primary:hover {
            background: #1b4fb0;
        }

        .btn-success {
            background: #22c55e;
            border-color: #22c55e;
            color: #fff;
        }

        .btn-success:hover {
            background: #16a34a;
        }

        .btn-danger {
            background: #ef4444;
            border-color: #ef4444;
            color: #fff;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn:active {
            transform: translateY(1px);
        }

        /* INFO BOX */
        .info-box {
            background: #fee;
            border: 1px solid #fcc;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .info-box p {
            font-size: 14px;
            color: #c33;
            margin: 0;
        }

        .info-box i {
            margin-right: 8px;
        }

        /* ALERT BOX */
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .alert i {
            font-size: 20px;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .cards-grid {
                grid-template-columns: 1fr;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .btn-group {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<div class="navbar">
    <h1><i class="fa-solid fa-user-edit"></i> Biodata Diri</h1>
    <a href="dashboard.php" class="btn-back">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
    </a>
</div>

<!-- CONTAINER -->
<div class="container">

    <?php if(isset($_SESSION['alert'])): ?>
    <div class="alert alert-warning">
        <i class="fa-solid fa-exclamation-triangle"></i>
        <strong><?= $_SESSION['alert'] ?></strong>
    </div>
    <?php unset($_SESSION['alert']); endif; ?>

    <!-- INFO BOX -->
    <div class="info-box">
        <p><i class="fa-solid fa-exclamation-circle"></i> <strong>Penting:</strong> Lengkapi semua data diri sebelum mengikuti tes seleksi.</p>
    </div>

    <!-- CARDS GRID -->
    <div class="cards-grid">
        <!-- UPLOAD FOTO CARD -->
        <div class="card">
            <h2><i class="fa-solid fa-image"></i> Upload Foto Profil</h2>
            <div class="foto-section">
                <img src="<?= $foto ?>" alt="Foto Profil">
                <p><?= !empty($user['foto']) ? 'Foto sudah diupload' : 'Belum ada foto yang diupload' ?></p>
                
                <form method="post" enctype="multipart/form-data" id="fotoForm">
                    <input type="file" name="foto" id="fotoInput" accept="image/jpeg,image/jpg,image/png" required style="display: none;">
                    <input type="hidden" name="update_foto" value="1">
                    <button type="button" class="btn btn-success" onclick="document.getElementById('fotoInput').click()">
                        <i class="fa-solid fa-upload"></i> Upload Foto
                    </button>
                </form>
            </div>
        </div>

        <!-- DATA DIRI CARD -->
    <div class="card">
        <h2><i class="fa-solid fa-user"></i> Data Pribadi</h2>
        
        <form method="post">
            <div class="form-grid">
                <div class="form-group full">
                    <label>Nama Lengkap <span style="color: red;">*</span></label>
                    <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" disabled>
                </div>

                <div class="form-group">
                    <label>Email <span style="color: red;">*</span></label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                </div>

                <div class="form-group">
                    <label>No. HP <span style="color: red;">*</span></label>
                    <input type="tel" name="no_hp" value="<?= htmlspecialchars($user['no_hp']) ?>" disabled>
                </div>

                <div class="form-group">
                    <label>Asal Sekolah <span style="color: red;">*</span></label>
                    <input type="text" name="asal_sekolah" value="<?= htmlspecialchars($user['asal_sekolah']) ?>" disabled>
                </div>

                <div class="form-group">
                    <label>Prodi Pilihan <span style="color: red;">*</span></label>
                    <input type="text" name="jurusan_pilihan" value="<?= htmlspecialchars($user['jurusan_pilihan']) ?>" disabled>
                </div>

                <div class="form-group full">
                    <label>Alamat Lengkap <span style="color: red;">*</span></label>
                    <textarea name="alamat" disabled><?= htmlspecialchars($user['alamat']) ?></textarea>
                </div>

                <div class="form-group">
                    <label>NIK <span style="color: red;">*</span></label>
                    <input type="text" name="nik" value="<?= htmlspecialchars($user['nik'] ?? '') ?>" required maxlength="16" pattern="[0-9]{16}" placeholder="Masukkan 16 digit NIK">
                </div>

                <div class="form-group">
                    <label>Tempat Lahir <span style="color: red;">*</span></label>
                    <input type="text" name="tempat_lahir" value="<?= htmlspecialchars($user['tempat_lahir'] ?? '') ?>" required placeholder="Contoh: Jakarta">
                </div>

                <div class="form-group">
                    <label>Tanggal Lahir <span style="color: red;">*</span></label>
                    <input type="date" name="tanggal_lahir" value="<?= htmlspecialchars($user['tanggal_lahir'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label>Jenis Kelamin <span style="color: red;">*</span></label>
                    <select name="jenis_kelamin" required>
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="Laki-laki" <?= (($user['jenis_kelamin'] ?? '')=='Laki-laki')?'selected':'' ?>>Laki-laki</option>
                        <option value="Perempuan" <?= (($user['jenis_kelamin'] ?? '')=='Perempuan')?'selected':'' ?>>Perempuan</option>
                    </select>
                </div>
            </div>

            <div class="btn-group">
                <button type="submit" name="update_data" class="btn btn-primary" <?= $foto_required ? 'disabled' : '' ?>>
                    <i>Simpan </i>
            </div>
        </form>
    </div>
    </div>

</div>

<script>
    document.getElementById('fotoInput').addEventListener('change', function() {
        if (this.files.length > 0) {
            if (confirm('Upload foto "' + this.files[0].name + '"?')) {
                document.getElementById('fotoForm').submit();
            }
        }
    });
</script>

</body>
</html>
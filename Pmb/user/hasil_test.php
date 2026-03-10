<?php
require_once '../config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT * FROM calon_mahasiswa WHERE id_calon = '$user_id'"
));

// Cek biodata lengkap
$required_fields = ['tempat_lahir', 'tanggal_lahir', 'jenis_kelamin'];
$biodata_lengkap = true;
foreach($required_fields as $field) {
    if (empty($user[$field])) {
        $biodata_lengkap = false;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Hasil Tes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        .header {
            background: #1f2344;
            color: #fff;
            padding: 12px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .header-left h1 {
            font-size: 18px;
            font-weight: 700;
        }

        .header-left i {
            font-size: 18px;
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

        .container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 0 20px;
        }

        /* BANNER */
        .banner {
            background: #1f2344;
            border-radius: 12px;
            padding: 50px 20px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }

        .banner::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 15s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(-10%, -10%); }
        }

        .banner h1 {
            position: relative;
            font-size: 32px;
            font-weight: 700;
            color: #fff;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* DETAIL BOX */
        .detail-box {
            background: #fff;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .detail-box h2 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 35px;
            color: #1c1e21;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .detail-box h2 i {
            color: #4267B2;
        }

        /* INFO GRID */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .info-card {
            background: #f5f7fa;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            transition: 0.3s;
        }

        .info-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .info-card h3 {
            font-size: 13px;
            margin-bottom: 15px;
            color: #9ca3af;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .info-card h3 i {
            color: #4267B2;
        }

        .info-card p {
            font-size: 22px;
            font-weight: 700;
            color: #1f2344;
        }

        /* RESULT MESSAGE */
        .result-message {
            background: #003d82;
            border-radius: 12px;
            padding: 50px 40px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0, 61, 130, 0.2);
            border: 2px solid #003d82;
        }

        .result-message h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 20px;
            margin-top: 0;
            color: #fff;
        }

        .result-message p {
            font-size: 15px;
            line-height: 1.8;
            margin: 15px 0;
            margin-bottom: 30px;
            color: #e0e7ff;
        }

        .result-message.tidak-lulus {
            background: #e74c3c;
            border-color: #e74c3c;
        }

        .result-message.tidak-lulus h2,
        .result-message.tidak-lulus p {
            color: #fff;
        }

        .biodata-box {
            background:#1f2344;
            border-radius: 16px;
            padding: 40px;
            text-align: center;
            color: #fff;
            box-shadow: 0 8px 24px rgba(36, 50, 110, 0.2);
        }

        .biodata-box i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.9;
        }

        .biodata-box h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .biodata-box p {
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 30px;
            opacity: 0.95;
        }

        .biodata-box .btn {
            color: #ffffff;
            padding: 5px 16px;
            border-radius: 6px;
            font-weight: 500;
            display: inline-block;
            font-size: 13px;
            text-decoration: none;
        }

        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        .btn-cetak {
            background: #6c757d;
            color: #fff;
            padding: 12px 28px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
            box-shadow: 0 2px 4px rgba(108, 117, 125, 0.3);
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-cetak:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(108, 117, 125, 0.4);
        }

        .btn-lanjut {
            background: #0099ff;
            color: #fff;
            padding: 12px 28px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
            box-shadow: 0 2px 4px rgba(0, 153, 255, 0.3);
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-lanjut:hover {
            background: #0084d6;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 153, 255, 0.4);
        }

        /* STATUS BADGES */
        .status-lulus {
            color: #fff;
            background: #22c55e;
            padding: 8px 24px;
            border-radius: 20px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            border: none;
            box-shadow: none;
        }

        .status-lulus i {
            font-size: 14px;
        }

        .status-tidak {
            color: #fff;
            background: #dc3545;
            padding: 8px 24px;
            border-radius: 20px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            border: none;
            box-shadow: none;
        }

        .status-tidak i {
            font-size: 14px;
        }

        /* NEXT STEP BOX */
        .next-step {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 2px solid #fbbf24;
            border-radius: 12px;
            padding: 40px 30px;
            text-align: center;
            margin-top: 35px;
        }

        .next-step h3 {
            font-size: 22px;
            color: #b45309;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .next-step h3 i {
            color: #fbbf24;
        }

        .next-step p {
            color: #92400e;
            font-size: 15px;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .next-step.belum-test {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border: 2px solid #fbbf24;
        }

        .next-step.belum-test h3 i {
            color: #fbbf24;
        }

        /* BUTTON */
        .btn {
            background: linear-gradient(135deg, #4267B2, #5578c7);
            color: #fff;
            padding: 12px 30px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: 0.2s;
            box-shadow: 0 2px 4px rgba(66,103,178,0.3);
        }

        .btn:hover {
            background: linear-gradient(135deg, #365899, #4267B2);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(66,103,178,0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #34d399);
            box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #059669, #10b981);
            box-shadow: 0 4px 8px rgba(16, 185, 129, 0.4);
        }

        /* RESPONSIVE */
        @media(max-width:768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .header {
                padding: 12px 20px;
            }
            
            .header-left h1 {
                font-size: 16px;
            }
            
            .banner h1 {
                font-size: 24px;
            }
            
            .detail-box {
                padding: 25px;
            }
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="header-left">
            <i class="fa-solid fa-chart-line"></i>
            <h1>Hasil Tes Seleksi</h1>
        </div>
        <a href="dashboard.php" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>

    <div class="container">

        <?php if (!$biodata_lengkap): ?>

            <div class="biodata-box">
                <h2>Lengkapi Biodata Terlebih Dahulu</h2>
                <p>Sebelum mengikuti tes masuk, Anda harus melengkapi biodata diri terlebih dahulu.<br>Pastikan semua data yang diisi sudah benar dan lengkap.</p>
                <a href="profil.php" class="btn">Lengkapi Biodata</a>
            </div>

        <?php elseif ($user['status_test'] == 'belum_test'): ?>

            <div class="biodata-box">
                <h2>Anda Belum Mengikuti Tes</h2>
                <p>Silakan ikuti tes terlebih dahulu untuk melihat hasil Anda</p>
                <a href="test.php" class="btn">Mulai Tes</a>
            </div>

        <?php else: ?>

            <?php if ($user['status_test'] == 'lulus'): ?>
                <div class="result-message lulus">
                    <h2>SELAMAT! ANDA DINYATAKAN LULUS</h2>
                    <p><strong><?php echo $user['nama_lengkap']; ?></strong> - Program Studi <strong><?php echo $user['jurusan_pilihan']; ?></strong></p>
                    <p>Berdasarkan hasil seleksi yang telah dilaksanakan, Saudara/i dinyatakan diterima sebagai Mahasiswa Universitas Virelta Indonesia. Dan dipersilakan melanjutkan ke tahap Daftar Ulang melalui sistem PMB Online.</p>
                    <div class="button-group">
                        <a href="daftar_ulang.php" class="btn-lanjut">
                            <i>Lanjut Daftar Ulang</i> 
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="result-message tidak-lulus">
                    <h2>MOHON MAAF, ANDA BELUM LULUS</h2>
                    <p><strong><?php echo $user['nama_lengkap']; ?></strong> - Program Studi <strong><?php echo $user['jurusan_pilihan']; ?></strong></p>
                    <p>Berdasarkan hasil seleksi yang telah dilakukan, Saudara/i belum dinyatakan diterima sebagai calon mahasiswa Universitas Virelta Indonesia Tahun Akademik 2026/2027.</p>
                    <p>Terima kasih atas partisipasi Anda dalam proses seleksi ini.</p>
                </div>
            <?php endif; ?>

        <?php endif; ?>

    </div>

</body>

</html>
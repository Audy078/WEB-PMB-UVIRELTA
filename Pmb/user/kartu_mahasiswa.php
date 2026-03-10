<?php
require_once '../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$user = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT * FROM calon_mahasiswa WHERE id_calon = '$user_id'"
));

// Cek apakah sudah daftar ulang
if ($user['status_daftar_ulang'] != 'sudah') {
    header('Location: dashboard.php');
    exit();
}

$foto = (!empty($user['foto']) && file_exists('../uploads/' . $user['foto']))
    ? '../uploads/' . $user['foto']
    : '../assets/image/default-user.png';

$logo = '../assets/logo.png';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Dokumen</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;font-family:sans-serif;}
        body{background:#f5f7fa;}

        /* Header */
        .header{
            background: #1f2344;
            color:#fff;
            padding:12px 30px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header h1{
            font-size:18px;
            font-weight:700;
            display:flex;
            align-items:center;
            gap:8px;
        }
        .nav-wrapper{display:flex;gap:10px;}
        .btn-back{
            background: rgba(255,255,255,0.2);
            color:#fff;
            padding:8px 20px;
            border-radius:20px;
            text-decoration:none;
            font-weight:600;
            transition:0.2s;
            display:inline-flex;
            align-items:center;
            gap:8px;
            font-size:14px;
        }
        .btn-back:hover{
            background: rgba(255,255,255,0.3);
        }

        /* Container */
        .container{
            max-width:1200px;
            margin:30px auto;
            padding:0 20px;
        }

        /* Document Selector */
        .doc-container{
            display:grid;
            grid-template-columns: 300px 1fr;
            gap:30px;
            margin-bottom:40px;
            align-items:start;
        }
        .doc-selector{
            display:flex;
            flex-direction:column;
            gap:15px;
        }
        .doc-item{
            background:#fff;
            border-radius:12px;
            padding:20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border: 2px solid #e8e8e8;
            cursor:pointer;
            transition: all 0.3s ease;
            text-align:center;
        }
        .doc-item-icon{
            font-size:40px;
            color:#4267B2;
            margin-bottom:15px;
        }
        .doc-item h3{
            font-size:16px;
            color:#1f2344;
            margin-bottom:8px;
        }
        .doc-item p{
            font-size:13px;
            color:#777;
            line-height:1.5;
        }
        .doc-item:hover{
            border-color:#4267B2;
            box-shadow: 0 4px 16px rgba(66,103,178,0.15);
            transform: translateY(-2px);
        }
        .doc-item.active{
            border-color:#4267B2;
            background:#f0f4ff;
        }

        .content-section{display:none;}
        .content-section.active{display:block;}

        /* Content Area */
        .doc-content-area{
            background:#fff;
            border-radius:12px;
            box-shadow:0 2px 8px rgba(0,0,0,0.08);
            padding:30px;
        }

        .btn-print{
            background:#28a745;
            color:#fff;
            padding:12px 28px;
            border-radius:12px;
            font-weight:600;
            border:none;
            cursor:pointer;
            transition:.3s;
            font-size:14px;
            display:inline-flex;
            align-items:center;
            gap:8px;
        }
        .btn-print:hover{background:#218838;}

        /* ===== Kartu Mahasiswa ===== */
        .student-card{
            width:100%;
            max-width:500px;
            height:300px;
            background: linear-gradient(135deg,#1c2366,#2e3bb8);
            border-radius:15px;
            padding:20px 25px;
            color:#fff;
            display:flex;
            flex-direction:column;
            justify-content:space-between;
            box-shadow:0 10px 25px rgba(0,0,0,.25);
            margin:0 auto;
        }

        .card-header{
            display:flex;
            align-items:center;
            gap:15px;
            margin-bottom:10px;
        }
        .card-header img{
            width:50px;
            height:50px;
            object-fit:contain;
            border-radius:5px;
        }
        .card-header h2{
            font-size:20px;
            font-weight:700;
        }

        .card-body{
            display:flex;
            gap:20px;
        }
        .photo{
            width:120px;
            height:150px;
            border-radius:8px;
            overflow:hidden;
            flex-shrink:0;
        }
        .photo img{
            width:100%;
            height:100%;
            object-fit:cover;
        }
        .info{
            display:flex;
            flex-direction:column;
            justify-content:center;
            font-size:16px;
        }
        .info p {
            margin-bottom: 8px;
            display: grid;
            grid-template-columns: 130px 1fr;
            gap: 2px;
            align-items: center;
        }
        .info strong {
            font-weight: 700;
        }
        .card-footer{
            display:flex;
            justify-content:space-between;
            font-size:14px;
        }

        /* ===== Dokumen Lainnya ===== */
        .document-content{
            background:#fff;
            border-radius:12px;
            padding:30px;
            box-shadow:0 2px 8px rgba(0,0,0,0.08);
            width:100%;
        }
        .document-content h2{
            text-align:center;
            color:#1f2344;
            margin-bottom:30px;
            font-size:20px;
        }
        .doc-info{
            margin-bottom:20px;
            line-height:1.8;
        }
        .doc-info p{
            margin-bottom:12px;
        }
        .print-button{
            text-align:center;
            margin-top:20px;
        }

        /* ===== Cetak ===== */
        @media print{
            body{background:none;}
            .header,.doc-selector,.btn-print,.print-button{display:none !important;}
            .content-section.active{display:block !important;}
            .content-section {display:none !important;}
            .student-card{
                width:85.6mm;  
                height:53.98mm;
                padding:5mm;
                border-radius:5px;
                background: linear-gradient(135deg,#1c2366,#2e3bb8);
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                box-shadow:none;
                margin:0;
            }
            .card-header{
                gap:2mm;
                margin-bottom:0.5mm;
            }
            .card-header img{
                width:10mm;
                height:10mm;
            }
            .card-header h2{font-size:7pt;}
            .card-body{gap:1.5mm;}
            .photo{
                width:20mm;
                height:25mm;
            }
            .info{
                font-size:8pt;
                line-height:1.2;
            }
            .info p {
                display: grid;
                grid-template-columns: 50px 1fr;
                gap: 1px;
                margin-bottom: 1px;
            }
            .card-footer{font-size:7pt;}
            .document-content{
                max-width:100%;
                padding:0;
                box-shadow:none;
                background:none;
            }
            .document-content h2{font-size:16px;margin-bottom:20px;}
        }

        @media(max-width:1024px){
            .doc-container{
                grid-template-columns: 250px 1fr;
            }
        }

        @media(max-width:768px){
            .doc-container{
                grid-template-columns:1fr;
            }
            .doc-selector{
                flex-direction:row;
                gap:10px;
                overflow-x:auto;
            }
            .doc-item{
                min-width:150px;
            }
            .student-card{width:100%;}
            .card-body{flex-direction:column;align-items:center;}
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-print"></i> Cetak Dokumen</h1>
        <div class="nav-wrapper">
            <a href="dashboard.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard</a>
        </div>
    </div>

    <div class="container">
        <!-- Document Selector and Content Container -->
        <div class="doc-container">
            <!-- Left: Document Selector -->
            <div class="doc-selector">
                <div class="doc-item active" onclick="showDoc('daftar_ulang')">
                    <div class="doc-item-icon"><i class="fas fa-clipboard-check"></i></div>
                    <h3>Bukti Daftar Ulang</h3>
                    <p>Cetak bukti pendaftaran ulang</p>
                </div>
                <div class="doc-item" onclick="showDoc('kartu')">
                    <div class="doc-item-icon"><i class="fas fa-id-card"></i></div>
                    <h3>Kartu Mahasiswa</h3>
                    <p>Cetak kartu identitas mahasiswa</p>
                </div>
                <div class="doc-item" onclick="showDoc('surat')">
                    <div class="doc-item-icon"><i class="fas fa-file-alt"></i></div>
                    <h3>Surat Pernyataan</h3>
                    <p>Cetak surat pernyataan diri</p>
                </div>
            </div>

            <!-- Right: Document Content -->
            <div style="display:flex;flex-direction:column;gap:20px;">

        <!-- Bukti Daftar Ulang -->
        <div id="daftar_ulang" class="content-section active">
            <div class="document-content">
                <h2>Bukti Daftar Ulang</h2>
                <div class="doc-info">
                    <p><strong>Nama Lengkap:</strong> <?= htmlspecialchars($user['nama_lengkap']) ?></p>
                    <p><strong>NIM:</strong> <?= htmlspecialchars($user['nim'] ?? '-') ?></p>
                    <p><strong>Nomor Tes:</strong> <?= htmlspecialchars($user['nomor_test'] ?? '-') ?></p>
                    <p><strong>Program Studi:</strong> <?= htmlspecialchars($user['jurusan_pilihan'] ?? '-') ?></p>
                </div>
                <hr style="margin:20px 0;">
                <div class="doc-info">
                    <p style="text-align:center;font-weight:bold;">Karena telah memenuhi semua persyaratan, Saudara/i dinyatakan telah berhasil melakukan Daftar Ulang sebagai Mahasiswa aktif Universitas Virelta Indonesia.</p>
                    <p style="text-align:center;margin-top:20px;"><strong>Tanggal Daftar Ulang:</strong> <?= date('d M Y') ?></p>
                </div>
            </div>
            <div class="print-button">
                <button onclick="window.print()" class="btn-print"><i class="fas fa-print"></i> Cetak Bukti Daftar Ulang</button>
            </div>
        </div>

        <!-- Kartu Mahasiswa -->
        <div id="kartu" class="content-section">
            <div class="student-card">
                <div class="card-header">
                     <img src="<?= $logo ?>" class="logo" alt="Logo Universitas">
                    <h2>UNIVERSITAS VIRELTA INDONESIA</h2>
                </div>

                <div class="card-body">
                    <div class="photo">
                        <img src="<?= $foto ?>" alt="Foto Mahasiswa">
                    </div>
                    <div class="info">
                        <p><strong>NIM</strong> : <?= $user['nim']; ?></p>
                        <p><strong>Nama</strong> : <?= $user['nama_lengkap']; ?></p>
                        <p><strong>Program Studi</strong> : <?= htmlspecialchars($user['jurusan_pilihan'] ?? '-') ?></p>
                        <p><strong>Status</strong> : AKTIF</p>
                    </div>
                </div>

                <div class="card-footer">
                    <span>Mahasiswa Aktif</span>
                    <span><?= date('Y'); ?></span>
                </div>
            </div>
            <div class="print-button">
                <button onclick="window.print()" class="btn-print"><i class="fas fa-print"></i> Cetak Kartu Mahasiswa</button>
            </div>
        </div>

        <!-- Surat Pernyataan -->
        <div id="surat" class="content-section">
            <div class="document-content">
                <h2>Surat Pernyataan</h2>
                <div class="doc-info" style="margin-top:30px;line-height:2;">
                    <p style="text-align:center;margin-bottom:30px;">SURAT PERNYATAAN MAHASISWA</p>
                    <p>Yang bertanda tangan di bawah ini:</p>
                    <p style="margin-left:30px;">
                        Nama &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?= htmlspecialchars($user['nama_lengkap']) ?><br>
                        NIM &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?= htmlspecialchars($user['nim'] ?? '-') ?><br>
                        Program Studi &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?= htmlspecialchars($user['jurusan_pilihan'] ?? '-') ?><br>
                        Tahun Akademik &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: 2026/2027
                    </p>
                    <p style="margin-top:20px;">Dengan ini menyatakan bahwa:</p>
                    <p style="margin-left:30px;">
                        1. Saya telah memenuhi semua persyaratan untuk diterima sebagai mahasiswa di Universitas Virelta Indonesia.<br>
                        2. Data-data yang saya isi adalah benar dan dapat dipertanggungjawabkan.<br>
                        3. Saya siap melaksanakan tugas dan tanggung jawab sebagai mahasiswa aktif.<br>
                        4. Saya mengerti dan menyetujui semua peraturan yang berlaku di universitas ini.
                    </p>
                    <p style="margin-top:30px;">Demikian pernyataan ini saya buat dengan sebenar-benarnya dan penuh kesadaran.</p>
                    <p style="margin-top:40px;margin-right:50px;text-align:right;">
                        Tanggal: <?= date('d M Y') ?><br><br><br>
                        ........................<br>
                        <?= htmlspecialchars($user['nama_lengkap']) ?>
                    </p>
                </div>
            </div>
            <div class="print-button">
                <button onclick="window.print()" class="btn-print"><i class="fas fa-print"></i> Cetak Surat Pernyataan</button>
            </div>
        </div>
            </div>
        </div>
    </div>

    <script>
        function showDoc(docType) {
            // Hide all content sections
            document.querySelectorAll('.content-section').forEach(el => {
                el.classList.remove('active');
            });
            // Remove active class from all doc items
            document.querySelectorAll('.doc-item').forEach(el => {
                el.classList.remove('active');
            });
            // Show selected content
            document.getElementById(docType).classList.add('active');
            // Add active class to clicked item
            event.target.closest('.doc-item').classList.add('active');
        }
    </script>
</body>
</html>

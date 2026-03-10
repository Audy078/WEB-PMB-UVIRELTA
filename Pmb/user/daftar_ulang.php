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

// Cek status lulus
if ($user['status_test'] != 'lulus') {
    header('Location: dashboard.php');
    exit();
}

$already_daftar_ulang = ($user['status_daftar_ulang'] == 'sudah');
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$already_daftar_ulang) {
    $nomor_orang_tua = preg_replace('/\D/', '', $_POST['nomor_orang_tua'] ?? '');

    if ($nomor_orang_tua === '') {
        $error = 'Nomor HP Orang Tua/Wali wajib diisi.';
    } elseif (strlen($nomor_orang_tua) < 10 || strlen($nomor_orang_tua) > 13) {
        $error = 'Nomor HP Orang Tua/Wali harus 10-13 digit.';
    } else {
        $nim = date('Y') . '01' . str_pad($user_id, 4, '0', STR_PAD_LEFT);

        mysqli_query($conn, "
            UPDATE calon_mahasiswa
            SET status_daftar_ulang = 'sudah',
                nim = '$nim',
                nomor_orang_tua = '$nomor_orang_tua'
            WHERE id_calon = '$user_id'
        ");

        header('Location: daftar_ulang.php?success=1');
        exit();
    }
}

$success = isset($_GET['success']) || $already_daftar_ulang;
if ($success) {
    $user = mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT * FROM calon_mahasiswa WHERE id_calon = '$user_id'"
    ));
    $already_daftar_ulang = true;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar Ulang Mahasiswa Baru</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: "Segoe UI", Tahoma, sans-serif;
    background: #f0f2f5;
    color: #1c1e21;
}

/* NAVBAR */
.navbar {
    background: #1f2344;
    padding: 12px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.navbar h1 {
    color: #fff;
    font-size: 18px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 8px;
}

.navbar a, .btn-back {
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

.navbar a:hover, .btn-back:hover {
    background: rgba(255,255,255,0.3);
}

/* CONTAINER */
.container {
    max-width: 1000px;
    margin: 30px auto;
    padding: 0 20px;
}

/* CARD */
.card {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

/* ALERT */
.alert-success {
    background: #f2f6ff;
    padding: 18px 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    border-left: 4px solid #1f2344;
    display: flex;
    align-items: center;
    gap: 14px;
}

.alert-success h2 {
    color: #1f2344;
    font-size: 18px;
    margin-bottom: 6px;
}

.alert-success p {
    font-size: 14px;
    color: #2b2f4a;
}

.alert-icon {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: #e1e8ff;
    color: #1f2344;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}

.error-box {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    padding: 12px 14px;
    border-radius: 8px;
    margin-bottom: 15px;
    font-size: 14px;
}

/* TITLE */
.card h3 {
    font-size: 20px;
    margin-bottom: 8px;
}

.card .desc {
    font-size: 14px;
    color: #65676b;
    margin-bottom: 20px;
}

/* GRID */
.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

/* READONLY BOX */
.form-box {
    background: #f8f9fa;
    padding: 16px;
    border-radius: 8px;
    font-size: 13px;
    color: #65676b;
    border: 1px solid #e9ecef;
}

.form-box strong {
    display: block;
    margin-top: 8px;
    color: #1c1e21;
    font-size: 14px;
}

/* INPUT FIELD */
.input-field {
    background: #fff;
    padding: 16px;
    border-radius: 8px;
    font-size: 14px;
    color: #1c1e21;
    grid-column: span 2;
    border: 1px solid #e9ecef;
}

.input-field label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #050505;
}

.input-field input,
.input-field select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    background: #fff;
}

.input-field input:focus,
.input-field select:focus {
    outline: none;
    border-color: #4267B2;
}

.input-field small {
    display: block;
    margin-top: 8px;
    color: #999;
}

/* CHECKBOX */
.checkbox-wrap {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 16px 18px;
    display: flex;
    gap: 12px;
    align-items: flex-start;
    cursor: pointer;
    transition: all 0.2s ease;
}

.checkbox-wrap:hover {
    background: #e9ecef;
    border-color: #adb5bd;
}

.checkbox-wrap input[type="checkbox"] {
    margin-top: 2px;
    width: 18px;
    height: 18px;
    cursor: pointer;
    flex-shrink: 0;
    accent-color: #4267B2;
}

.checkbox-wrap span {
    cursor: pointer;
    font-size: 14px;
    line-height: 1.5;
    color: #1c1e21;
    margin: 0;
    user-select: none;
}

/* BUTTON */
.btn-confirm {
    display: block;
    margin: 30px auto 0;
    background: #4267B2;
    color: #fff;
    border: none;
    padding: 13px 36px;
    font-size: 15px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(66, 103, 178, 0.3);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn-confirm:hover {
    background: #365899;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(66, 103, 178, 0.4);
}

.btn-confirm:active {
    transform: translateY(0);
}

.btn-confirm:disabled {
    background: #6c757d;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* SUCCESS */
.success-card {
    background: #003d82;
    padding: 45px 30px;
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    color: #fff;
    max-width: 700px;
    margin: 0 auto;
    border: 2px solid #003d82;
}

.success-card h2 {
    font-size: 32px;
    margin-bottom: 25px;
    font-weight: 700;
    color: #fff;
    text-align: center;
    letter-spacing: 0.5px;
}

.success-headline {
    background: #fff;
    color: #003d82;
    border-radius: 14px;
    padding: 22px 18px;
    margin-bottom: 30px;
    text-align: center;
    font-size: clamp(18px, 2.2vw, 32px);
    font-weight: 800;
    letter-spacing: 1px;
    text-transform: uppercase;
    line-height: 1.2;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
}

.success-card > p:first-of-type {
    font-size: 16px;
    margin-bottom: 30px;
    color: #374151;
}

/* Status badges */
.status-info {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin: 30px 0 35px 0;
}

.status-badge {
    font-size: 14px;
    font-weight: 600;
    color: #fff;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 11px;
    background: linear-gradient(135deg, #003d82 0%, #0f5a99 100%);
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0, 61, 130, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.status-badge:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 61, 130, 0.35);
}


.status-badge .badge-label {
    font-size: 11px;
    opacity: 0.85;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge .badge-value {
    font-size: 16px;
    font-weight: 700;
    letter-spacing: 0.3px;
}

/* Data summary section */
.data-summary {
    background: #003d82;
    border: 2px solid #003d82;
    border-radius: 12px;
    padding: 30px;
    margin: 30px 0;
}

.data-summary h3 {
    font-size: 18px;
    font-weight: 700;
    color: #fff;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 10px;
    padding-bottom: 15px;
    border-bottom: 2px solid #ffffff;
}

.summary-row {
    display: flex;
    padding: 15px 0;
    font-size: 14px;
    align-items: center;
    border-bottom: 1px solid rgba(255,255,255,0.2);
}

.summary-row:last-child {
    border-bottom: none;
}

.summary-row.highlight {
    background: #eff6ff;
    padding: 15px;
    border-radius: 8px;
    margin: 10px 0;
    border-bottom: none !important;
    border-left: 4px solid #3b82f6;
}

.summary-label {
    font-weight: 700;
    color: #e0e7ff;
    min-width: 150px;
    flex-shrink: 0;
}

.summary-value {
    flex: 1;
    color: #e0e7ff;
    word-break: break-word;
    padding-left: 20px;
}

.summary-row.highlight .summary-value {
    color: #1f2344;
    font-weight: 600;
}

/* Message box */
.message-box {
    background: #fff;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 25px;
    margin: 30px 0;
}

.message-title {
    font-weight: 700;
    color: #003d82;
    margin-bottom: 10px;
    font-size: 16px;
    margin: 0 0 12px 0;
}

.message-text {
    font-size: 14px;
    color: #374151;
    line-height: 1.7;
    margin: 0;
}

.nim-box {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    border: 2px solid #1d4ed8;
    margin: 20px auto 10px auto;
    padding: 25px 40px;
    font-size: 36px;
    font-weight: 700;
    border-radius: 12px;
    width: fit-content;
    letter-spacing: 4px;
    color: #fff;
    box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
    text-align: center;
}

.nim-label {
    font-size: 13px;
    margin-bottom: 30px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #1f2344;
    text-align: center;
}

.detail-info {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 30px;
    margin: 30px auto;
    max-width: 100%;
    text-align: left;
}

.detail-row {
    display: flex;
    padding: 12px 0;
    border-bottom: 1px solid #e5e7eb;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 600;
    min-width: 180px;
    padding-left: 10px;
    color: #1f2344;
}

.detail-value {
    flex: 1;
    word-break: break-word;
    color: #374151;
}

.date-info {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
    font-size: 13px;
    text-align: center;
    color: #374151;
}

.date-info i {
    margin-right: 6px;
    color: #1f2344;
}

.action-row {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-top: 35px;
    flex-wrap: wrap;
}

.btn-secondary {
    background: #1f2344;
    color: #fff;
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: none;
    font-size: 14px;
    cursor: pointer;
}

.btn-secondary:hover {
    background: #0f1929;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(31, 35, 68, 0.3);
}

.btn-print {
    background: #17a2b8;
    cursor: pointer;
}

.btn-print:hover {
    background: #138496;
    box-shadow: 0 5px 15px rgba(23, 162, 184, 0.3);
}

.btn-surat {
    background: #28a745;
}

.btn-surat:hover {
    background: #218838;
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
}

.btn-kartu {
    background: #6c757d;
}

.btn-kartu:hover {
    background: #5a6268;
    box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .success-card {
        padding: 25px 20px;
    }
    
    .success-card h2 {
        font-size: 26px;
        margin-bottom: 20px;
    }
    
    .nim-box {
        font-size: 28px;
        padding: 18px 30px;
        letter-spacing: 2px;
        margin: 20px auto;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
    }
    
    .nim-label {
        margin-bottom: 25px;
        font-size: 12px;
    }
    
    .status-info {
        grid-template-columns: 1fr;
        gap: 12px;
        margin: 25px 0 30px 0;
    }
    
    .status-badge {
        padding: 16px;
        font-size: 13px;
    }
    
    .status-badge .badge-value {
        font-size: 14px;
    }
    
    .data-summary {
        padding: 20px;
        margin: 25px 0;
    }
    
    .data-summary h3 {
        font-size: 16px;
        margin-bottom: 20px;
    }
    
    .summary-row {
        flex-direction: column;
        padding: 12px 0;
    }
    
    .summary-label {
        min-width: auto;
        margin-bottom: 6px;
    }
    
    .summary-value {
        padding-left: 0;
    }
    
    .message-box {
        padding: 18px;
        margin: 25px 0;
    }
    
    .message-text {
        font-size: 13px;
        line-height: 1.6;
    }
    
    .action-row {
        flex-direction: column;
        gap: 10px;
        margin-top: 25px;
    }
    
    .btn-secondary {
        width: 100%;
        justify-content: center;
        padding: 11px 20px;
        font-size: 13px;
    }
    
    .print-data {
        padding: 15px;
        margin: 15px 0;
    }
}
</style>
</head>

<body>

<div class="navbar">
    <h1><i class="fas fa-user-graduate"></i> Daftar Ulang Mahasiswa Baru</h1>
    <a href="dashboard.php"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
</div>

<div class="container">

<?php if ($success): ?>

    <div class="success-card">
        <div class="success-headline">Daftar Ulang Berhasil</div>
        
        <div class="data-summary">
            <h3>DATA MAHASISWA</h3>
            
            <div class="summary-row">
                <span class="summary-label">Nama:</span>
                <span class="summary-value"><?= htmlspecialchars($user['nama_lengkap']) ?></span>
            </div>
            <div class="summary-row">
                <span class="summary-label">NIM:</span>
                <span class="summary-value"><?= htmlspecialchars($user['nim']) ?></span>
            </div>
            <div class="summary-row">
                <span class="summary-label">No. Tes:</span>
                <span class="summary-value"><?= htmlspecialchars($user['nomor_test']) ?></span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Program Studi:</span>
                <span class="summary-value"><?= htmlspecialchars($user['jurusan_pilihan']) ?></span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Email:</span>
                <span class="summary-value"><?= htmlspecialchars($user['email']) ?></span>
            </div>
            <div class="summary-row">
                <span class="summary-label">No. HP Orang Tua/Wali:</span>
                <span class="summary-value"><?= htmlspecialchars($user['nomor_orang_tua']) ?></span>
            </div>
        </div>
        
        <div class="message-box">
            <p class="message-title">Informasi</p>
            <p class="message-text">Silakan mencetak dokumen yang tersedia sebagai bukti telah menyelesaikan proses Daftar Ulang. Dokumen wajib disimpan dan dibawa saat kegiatan awal perkuliahan.</p>
        </div>
        
        <div class="action-row">
            <button onclick="window.location.href='kartu_mahasiswa.php'" class="btn-secondary btn-print">
                <i class="fas fa-print"></i> Cetak Dokumen
            </button>
        </div>
    </div>

<?php else: ?>

    <div class="card">

        <div style="background: #fee2e2; border: 1px solid #fca5a5; border-radius: 8px; padding: 16px; margin-bottom: 20px;">
            <h3 style="margin: 0 0 6px 0; font-size: 16px; color: #991b1b;">Informasi Mahasiswa (Read-Only)</h3>
            <p style="margin: 0; font-size: 14px; color: #b91c1c;">Periksa data berikut sebelum melakukan konfirmasi daftar ulang.</p>
        </div>

        <div class="form-grid">
            <div class="form-box">
                Nama Lengkap
                <strong><?php echo htmlspecialchars($user['nama_lengkap']); ?></strong>
            </div>

            <div class="form-box">
                Nomor Tes
                <strong><?php echo htmlspecialchars($user['nomor_test']); ?></strong>
            </div>

            <div class="form-box">
                Program Studi Diterima
                <strong><?php echo htmlspecialchars($user['jurusan_pilihan']); ?></strong>
            </div>

            <div class="form-box">
                Status
                <strong>LULUS</strong>
            </div>
        </div>

        <div style="background: #fee2e2; border: 1px solid #fca5a5; border-radius: 8px; padding: 16px; margin-bottom: 20px; margin-top: 20px;">
            <h3 style="margin: 0 0 6px 0; font-size: 16px; color: #991b1b;">Data yang dapat di isi</h3>
            <p style="margin: 0; font-size: 14px; color: #b91c1c;">Isi data berikut untuk melengkapi proses daftar ulang.</p>
        </div>

        <?php if ($error): ?>
            <div class="error-box"><?= $error; ?></div>
        <?php endif; ?>

        <form method="POST" id="daftarUlangForm">
            <div class="form-grid" style="margin-top: 15px;">
                <div class="input-field">
                    <label for="nomor_orang_tua">Nomor HP Orang Tua/Wali <span style="color: red;">*</span></label>
                    <input
                        type="tel"
                        id="nomor_orang_tua"
                        name="nomor_orang_tua"
                        placeholder="Contoh: 08123456789"
                        pattern="[0-9]{10,13}"
                        required
                    >
                    <small>Masukkan nomor HP orang tua/wali yang dapat dihubungi (10-13 digit)</small>
                </div>
            </div>

            <button type="submit" class="btn-confirm"
                onclick="return confirm('Apakah Anda yakin ingin melakukan daftar ulang?')">
                Konfirmasi Daftar Ulang
            </button>
        </form>

    </div>

<?php endif; ?>

</div>

<script>
// Validasi nomor HP
const nomorOrtu = document.getElementById('nomor_orang_tua');

if (nomorOrtu) {
    nomorOrtu.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
}
</script>

</body>
</html>

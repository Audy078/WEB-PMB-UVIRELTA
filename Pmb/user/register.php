<?php
require_once '../config.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = clean($_POST['nama_lengkap']);
    $email = clean($_POST['email']);
    $password = clean($_POST['password']);
    $no_hp = clean($_POST['no_hp']);
    $alamat = clean($_POST['alamat']);
    $asal_sekolah = clean($_POST['asal_sekolah']);
    $jurusan = clean($_POST['jurusan_pilihan']);

    if (empty($jurusan) || $jurusan === '-- Pilih Prodi --') {
        $error = 'Program Studi wajib dipilih!';
    } else {
        $check = mysqli_query($conn, "SELECT * FROM calon_mahasiswa WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = 'Email sudah terdaftar!';
        } else {
            // Generate nomor test dengan format PMB
            $last_nomor = mysqli_query($conn, "SELECT nomor_test FROM calon_mahasiswa ORDER BY id_calon DESC LIMIT 1");
            $last = mysqli_fetch_assoc($last_nomor);
            
            if ($last && $last['nomor_test'] && strpos($last['nomor_test'], 'PMB') === 0) {
                $num = intval(substr($last['nomor_test'], 3)) + 1;
            } else {
                $num = 1;
            }
            $nomor_test = 'PMB' . str_pad($num, 4, '0', STR_PAD_LEFT);
            
            mysqli_query($conn,"INSERT INTO calon_mahasiswa
            (nama_lengkap,email,password,no_hp,alamat,asal_sekolah,jurusan_pilihan,nomor_test)
            VALUES
            ('$nama','$email','$password','$no_hp','$alamat','$asal_sekolah','$jurusan','$nomor_test')");
            $success = 'Registrasi berhasil';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Registrasi Mahasiswa Baru</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
*{box-sizing:border-box;font-family:'Segoe UI',sans-serif}
body{
    background:#ffffff;
    margin:0;
}

.page{
    min-height:calc(100vh - 75px);
    padding:40px 20px 80px;
    display:flex;
    flex-direction:column;
    align-items:center;
}

/* NAVBAR */
.navbar{
    height:75px;
    background:#fff;
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:0 80px;
    box-shadow:0 2px 10px rgba(0,0,0,0.05);
    position:sticky;
    top:0;
    z-index:100;
}
.nav-left{display:flex;align-items:center;gap:15px}
.nav-left img{width:45px}
.nav-menu{display:flex;gap:20px;align-items:center}
.nav-menu a{text-decoration:none;font-size:14px;font-weight:500;color:#333;position:relative}
.nav-menu a:hover{color:#1f2344}
.nav-menu a::after{content:'';position:absolute;left:0;bottom:-6px;width:0;height:2px;background:#1f2344;transition:0.3s}
.nav-menu a:hover::after{width:100%}
.nav-menu a.btn-daftar::after,
.nav-menu a.btn-login-nav::after{content:none}
.btn-daftar{padding:8px 20px;background:#ffcc00;border:none;border-radius:6px;font-weight:bold;cursor:pointer;text-decoration:none;color:#000;display:inline-flex;align-items:center;vertical-align:middle}
.btn-login-nav{padding:8px 22px;background:#0051ff;border:none;color:#fff!important;border-radius:6px;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;vertical-align:middle;font-weight:500}

.register-title{
    text-align:center;
    margin:30px 0 10px;
    color:#1e2470;
    font-size:28px;
}
.register-subtitle{
    text-align:center;
    color:#6b7280;
    margin-bottom:30px;
    max-width:700px;
}

.wrapper{
    width:100%;
    max-width:900px;
    background:#1e2470;
    border-radius:12px;
    padding:30px 30px 35px;
    box-shadow:0 12px 30px rgba(0,0,0,0.12);
    color:#fff;
}

.form-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:24px;
}
.form-group label{font-weight:600;margin-bottom:8px;display:block}
.form-group label{color:#fff}
.form-group input,
.form-group select,
.form-group textarea{
    width:100%;
    padding:12px;
    border:1px solid #d0d7e2;
    background:#ffffff;
    border-radius:6px;
    font-size:15px;
}
.form-group textarea{grid-column:1/-1;min-height:120px}
.full{grid-column:1/-1}
.privacy{
    display:flex;
    align-items:flex-start;
    gap:10px;
    margin-top:12px;
    color:#e5e7eb;
    font-size:14px;
}
.privacy input{margin-top:3px}
.privacy a{color:#aaccff;text-decoration:none}
.privacy .em{color:#6aa7ff;font-weight:600}
.btn{
    margin-top:24px;
    width:100%;
    background:#9ecbff;
    color:#0b1b4d;
    border:none;
    padding:14px;
    font-size:18px;
    border-radius:6px;
    cursor:pointer;
    letter-spacing:0.5px;
}
.footer-text{text-align:center;margin-top:20px}
.footer-text a{color: #aaccff;font-weight:600;text-decoration:none}
.alert{padding:12px;margin-bottom:16px;border-radius:8px}
.success{background:#d4edda;color:#155724}
.error{background:#f8d7da;color:#721c24}
.required-mark{color:#ff0000}

@media(max-width:900px){
    .navbar{padding:0 20px;height:auto;flex-wrap:wrap;gap:10px 20px;padding-bottom:10px}
    .nav-menu{flex-wrap:wrap;gap:12px 16px}
}

@media(max-width:768px){
    .form-grid{grid-template-columns:1fr}
    .wrapper{padding:24px}
}
</style>
</head>
<body>

<div class="navbar">
    <div class="nav-left">
        <img src="../assets/logo.png" alt="Universitas Virelta Indonesia">
        <div>
            <strong>PMB</strong><br>
            <small>Universitas Virelta Indonesia</small>
        </div>
    </div>

    <div class="nav-menu">
        <a href="../index.php">Beranda</a>
        <a class="btn-daftar" href="register.php">Daftar</a>
        <a class="btn-login-nav" href="login.php">Login</a>
    </div>
</div>

<div class="page">
    <h1 class="register-title">Registrasi Mahasiswa Baru</h1>
    <p class="register-subtitle">Lengkapi data diri Anda untuk mendaftar</p>

    <div class="wrapper">
        <?php if($success):?><div class="alert success"><?=$success?></div><?php endif;?>
        <?php if($error):?><div class="alert error"><?=$error?></div><?php endif;?>

        <form method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label>Nama Lengkap <span class="required-mark">*</span></label>
                    <input name="nama_lengkap" placeholder="Masukkan nama lengkap" required>
                </div>
                <div class="form-group">
                    <label>Email <span class="required-mark">*</span></label>
                    <input name="email" type="email" required>
                </div>
                <div class="form-group">
                    <label>Password <span class="required-mark">*</span></label>
                    <input name="password" type="password" required>
                </div>
                <div class="form-group">
                    <label>No. HP <span class="required-mark">*</span></label>
                    <input name="no_hp" placeholder="08xxxxxxxxxx" required>
                </div>
                <div class="form-group">
                    <label>Asal Sekolah <span class="required-mark">*</span></label>
                    <input name="asal_sekolah" placeholder="Nama sekolah" required>
                </div>
                <div class="form-group">
                    <label>Program Studi <span class="required-mark">*</span></label>
                    <select name="jurusan_pilihan" required>
                        <option value="">-- Pilih Prodi --</option>
                        <option>Teknik Informatika</option>
                        <option>Sistem Informasi</option>
                        <option>Manajemen</option>
                        <option>Akuntansi</option>
                    </select>
                </div>
                <div class="form-group full">
                    <label>Alamat Lengkap <span class="required-mark">*</span></label>
                    <textarea name="alamat" placeholder="Masukkan alamat lengkap"></textarea>
                </div>
            </div>

            <label class="privacy">
                <input type="checkbox" name="privacy_policy" required>
                <span>Dengan mendaftar, Anda setuju dengan <span class="em">Syarat &amp; Ketentuan</span> dan <span class="em">Kebijakan Privasi</span>.</span>
            </label>

            <button class="btn">Daftar Sekarang</button>
        </form>

        <div class="footer-text">
            <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
        </div>
    </div>
</div>

</body>
</html>

<?php
$loginUrl = 'user/login.php';
$registerUrl = 'user/register.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PMB - Universitas Virelta Indonesia</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', sans-serif;
}

/* ================= NAVBAR ================= */
.navbar {
    height: 75px;
    background: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 80px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    position: sticky;
    top: 0;
    z-index: 100;
}

.nav-left {
    display: flex;
    align-items: center;
    gap: 15px;
}

.nav-left img {
    width: 45px;
}

.nav-menu {
    display: flex;
    gap: 20px;
    align-items: center;
}

.nav-menu a {
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    color: #333;
    position: relative;
}

.nav-menu a:hover {
    color: #1f2344;
}

.nav-menu a::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: -6px;
    width: 0;
    height: 2px;
    background: #1f2344;
    transition: 0.3s;
}

.nav-menu a:hover::after {
    width: 100%;
}

.nav-menu a.btn-daftar::after,
.nav-menu a.btn-login-nav::after {
    content: none;
}

/* ================= LOGIN MODAL ================= */
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.45);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 200;
}

.modal-overlay.is-open {
    display: flex;
}

.modal {
    background: #fff;
    width: 90%;
    max-width: 420px;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    text-align: center;
}

.modal h3 {
    margin-bottom: 18px;
    color: #1f2344;
}

.modal-actions {
    display: grid;
    gap: 12px;
}

.modal-actions a {
    display: inline-block;
    padding: 10px 16px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
}

.modal-actions a.admin {
    background: #1f2344;
    color: #fff;
}

.modal-actions a.user {
    background: #ffcc00;
    color: #000;
}

.modal-close {
    margin-top: 16px;
    background: transparent;
    border: none;
    color: #333;
    cursor: pointer;
}

/* BUTTON DAFTAR */
.btn-daftar {
    padding: 8px 20px;
    background: #ffcc00;
    border: none;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
    text-decoration: none;
    color: #000;
    display: inline-block;
}

/* BUTTON LOGIN */
.btn-login-nav {
    padding: 8px 22px;
    background: #0051ff;
    border: none;
    color: #fff;
    border-radius: 6px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    font-weight: 500;
}

/* ================= HERO ================= */
.hero {
    height: 90vh;
    background: #1f2344;
    color: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 80px;
    gap: 60px;
}

.hero-content {
    max-width: 600px;
    flex: 1;
}

.hero-image {
    flex: 1;
    max-width: 500px;
}

.hero-image img {
    width: 100%;
    height: auto;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

.hero h1 {
    font-size: 48px;
    margin-bottom: 20px;
}

.hero p {
    margin-bottom: 30px;
    font-size: 16px;
    line-height: 1.6;
}

.hero button,
.hero a {
    padding: 12px 30px;
    background: #ffcc00;
    border: none;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
    text-decoration: none;
    color: #000;
    display: inline-block;
}

/* ================= SECTION ================= */
.section {
    padding: 80px;
}

.section h2 {
    text-align: center;
    margin-bottom: 50px;
    font-size: 30px;
    color: #1f2344;
}

.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px,1fr));
    gap: 30px;
}

.card {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    text-align: center;
}

#mengapa .card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

#mengapa .card:hover {
    transform: translateY(-6px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.12);
}

#prodi .card {
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}

#prodi .card:hover {
    transform: scale(1.03);
    box-shadow: 0 12px 28px rgba(0,0,0,0.18);
}

.card img {
    width: 60px;
    height: 60px;
    object-fit: contain;
    margin-bottom: 15px;
}

.step-number {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #1f2344;
    color: #fff;
    font-weight: 700;
    font-size: 20px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 15px;
}

.card h3 {
    margin-bottom: 15px;
    color: #1f2344;
}

.prodi {
    background: #1f2344;
    color:white;
}

.prodi h2 {
    color: #fff;
}

.prodi .card h3 {
    color: #1f2344;
}

/* ================= FOOTER ================= */
.footer {
    background: #ffffff;
    color: black;
    text-align: center;
    padding: 25px;
}
</style>
</head>
<body>

<div class="navbar">
    <div class="nav-left">
        <img src="assets/logo.png" alt="Universitas Virelta Indonesia">
        <div>
            <strong>Portal PMB</strong><br>
            <small>Universitas Virelta Indonesia</small>
        </div>
    </div>

    <div class="nav-menu">
        <a href="#">Beranda</a>
        <a class="btn-daftar" href="<?php echo $registerUrl; ?>">Daftar</a>
        <button class="btn-login-nav" type="button" id="login-btn">Login</button>
    </div>
</div>

<!-- HERO -->
<section class="hero">
    <div class="hero-content">
        <h1>PMB Universitas Virelta Indonesia</h1>
        <p>Saatnya wujudkan impianmu!
Pendaftaran Mahasiswa Baru 2026/2027 resmi dibuka.</p>
        <a href="<?php echo $registerUrl; ?>">Daftar Sekarang</a>
    </div>
    <div class="hero-image">
        <img src="assets/img1.jpg" alt="Universitas Virelta Indonesia">
    </div>
</section>


<div class="modal-overlay" id="login-modal" aria-hidden="true">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="login-title">
        <h3 id="login-title">Pilih Login</h3>
        <div class="modal-actions">
            <a class="admin" href="admin/login.php">Login Admin</a>
            <a class="user" href="<?php echo $loginUrl; ?>">Login Calon Mahasiswa</a>
        </div>
        <button class="modal-close" type="button" id="login-close">Tutup</button>
    </div>
</div>

<script>
const loginBtn = document.getElementById('login-btn');
const loginModal = document.getElementById('login-modal');
const loginClose = document.getElementById('login-close');

loginBtn.addEventListener('click', () => {
    loginModal.classList.add('is-open');
    loginModal.setAttribute('aria-hidden', 'false');
});

loginClose.addEventListener('click', () => {
    loginModal.classList.remove('is-open');
    loginModal.setAttribute('aria-hidden', 'true');
});

loginModal.addEventListener('click', (event) => {
    if (event.target === loginModal) {
        loginModal.classList.remove('is-open');
        loginModal.setAttribute('aria-hidden', 'true');
    }
});
</script>

</body>
</html>
    
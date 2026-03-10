<?php
require_once '../config.php';

if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean($_POST['username']);
    $password = clean($_POST['password']);

    $query = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $admin = mysqli_fetch_assoc($result);
        $_SESSION['admin_id'] = $admin['id_admin'];
        $_SESSION['admin_name'] = $admin['nama_lengkap'];
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login Admin PMB</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            min-height: 100vh;
            background: #ffffff;
        }

        .page {
            min-height: calc(100vh - 75px);
            padding: 40px 20px 80px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* NAVBAR */
        .navbar {
            height: 75px;
            background: #fff;
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
            gap: 35px;
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
            color: #1e2470;
        }

        .nav-menu a::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -6px;
            width: 0;
            height: 2px;
            background: #1e2470;
            transition: 0.3s;
        }

        .nav-menu a:hover::after {
            width: 100%;
        }

        .nav-menu a.btn-daftar::after,
        .nav-menu a.btn-login-nav::after {
            content: none;
        }

        .btn-daftar {
            padding: 8px 20px;
            background: #ffcc00;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            color: #000;
            display: inline-flex;
            align-items: center;
            vertical-align: middle;
        }

        .btn-login-nav {
            padding: 8px 22px;
            background: #0051ff;
            border: none;
            color: #fff !important;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            vertical-align: middle;
            font-weight: 500;
        }

        .login-title {
            text-align: center;
            margin: 30px 0 10px;
            color: #1e2470;
            font-size: 28px;
        }

        .login-subtitle {
            text-align: center;
            color: #6b7280;
            margin-bottom: 30px;
            max-width: 700px;
        }

        .login-wrapper {
            width: 100%;
            max-width: 760px;
            background: #1e2470;
            border-radius: 12px;
            padding: 30px 30px 35px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.12);
            color: #fff;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #fff;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #d0d7e2;
            background: #ffffff;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: #9ecbff;
            color: #0b1b4d;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            letter-spacing: 0.5px;
        }

        .error {
            background: rgba(255, 0, 0, .2);
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        @media (max-width: 900px) {
            .navbar {
                padding: 0 20px;
                height: auto;
                flex-wrap: wrap;
                gap: 10px 20px;
                padding-bottom: 10px;
            }

            .nav-menu {
                flex-wrap: wrap;
                gap: 12px 16px;
            }
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
            <a href="../index.php#mengapa">Panduan Calon Mahasiswa</a>
            <a href="../index.php#prodi">Program Studi</a>
            <a class="btn-daftar" href="../user/register.php">Daftar</a>
            <a class="btn-login-nav" href="login.php">Login</a>
        </div>
    </div>

    <div class="page">
        <h1 class="login-title">Login Admin PMB</h1>
        <p class="login-subtitle">Sistem Penerimaan Mahasiswa Baru Universitas Virelta Indonesia</p>

        <div class="login-wrapper">
            <?php if ($error): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Username :</label>
                    <input type="text" name="username" required>
                </div>

                <div class="form-group">
                    <label>Password :</label>
                    <input type="password" name="password" required>
                </div>

                <button class="btn-login">Masuk</button>
            </form>
        </div>
    </div>

</body>

</html>
<?php
require_once '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: hasil_test.php');
    exit();
}

$id = $_GET['id'];

$data = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT * FROM calon_mahasiswa WHERE id_calon = '$id'"
));

if (!$data) {
    header('Location: hasil_test.php');
    exit();
}

if (isset($_POST['simpan'])) {
    $nilai = $_POST['nilai_test'];
    $status = $_POST['status_test'];

    mysqli_query(
        $conn,
        "UPDATE calon_mahasiswa 
         SET nilai_test='$nilai', status_test='$status' 
         WHERE id_calon='$id'"
    );

    header('Location: hasil_test.php');
    exit();
}

$page = 'hasil_test.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Hasil Tes</title>

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
            height: 100vh
        }

        .sidebar {
            width: 230px;
            background: #1f2a6d;
            color: #fff;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .sidebar-top {
            flex: 1
        }

        .sidebar h2 {
            margin-bottom: 25px
        }

        .sidebar ul {
            list-style: none
        }

        .sidebar ul li a {
            display: block;
            padding: 12px;
            border-radius: 8px;
            color: #fff;
            text-decoration: none;
            margin-bottom: 5px;
        }

        .sidebar ul li.active a,
        .sidebar ul li a:hover {
            background: #ffffff33
        }

        .logout {
            background: #fff;
            color: #1f2a6d;
            padding: 10px;
            border-radius: 20px;
            text-align: center;
            text-decoration: none;
            font-weight: bold;
        }

        .main {
            flex: 1;
            display: flex;
            flex-direction: column
        }

        .topbar {
            background: #f1f1f1;
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .content {
            padding: 25px
        }

        .card {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            max-width: 600px;
        }

        .card h3 {
            background: #1f2a6d;
            color: #fff;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px
        }

        label {
            font-weight: 600;
            display: block;
            margin-bottom: 5px
        }

        input,
        select {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        .btn {
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            border: none;
            cursor: pointer;
        }

        .btn-simpan {
            background: #1f2a6d;
            color: #fff;
        }

        .btn-batal {
            background: #ccc;
            color: #000;
        }
    </style>
</head>

<body>
    <div class="wrapper">

        <!-- SIDEBAR -->
        <aside class="sidebar">
            <div class="sidebar-top">
                <h2>Menu</h2>
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="soal.php">Kelola Soal Tes</a></li>
                    <li><a href="list_pendaftar.php">List Pendaftaran</a></li>
                    <li class="active"><a href="hasil_test.php">Hasil Tes</a></li>
                    <li><a href="daftar_ulang.php">Data Daftar Ulang</a></li>
                </ul>
            </div>
            <a href="logout.php" class="logout">Log out</a>
        </aside>

        <!-- MAIN -->
        <main class="main">

            <header class="topbar">
                <div><strong> Edit Hasil Tes</strong></div>
                <div>Hi!
                    <?= $_SESSION['admin_name'] ?>
                </div>
            </header>

            <section class="content">
                <div class="card">
                    <h3>Edit Hasil Tes</h3>

                    <form method="post">
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" value="<?= $data['nama_lengkap'] ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label>Nomor Tes</label>
                            <input type="text" value="<?= $data['nomor_test'] ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label>Nilai Tes</label>
                            <input type="number" name="nilai_test" value="<?= $data['nilai_test'] ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Status Tes</label>
                            <select name="status_test" required>
                                <option value="lulus" <?= $data['status_test'] == 'lulus' ? 'selected' : '' ?>>LULUS
                                </option>
                                <option value="tidak_lulus" <?= $data['status_test'] == 'tidak_lulus' ? 'selected' : '' ?>
                                    >TIDAK LULUS</option>
                            </select>
                        </div>

                        <button type="submit" name="simpan" class="btn btn-simpan">Simpan</button>
                        <a href="hasil_test.php" class="btn btn-batal">Batal</a>
                    </form>

                </div>
            </section>

        </main>
    </div>
</body>

</html>
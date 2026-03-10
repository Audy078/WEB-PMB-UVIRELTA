<?php
require_once '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

/* DELETE */
if (isset($_GET['delete'])) {
    $id = clean($_GET['delete']);
    mysqli_query($conn, "DELETE FROM soal WHERE id_soal='$id'");
    header('Location: soal.php?msg=deleted');
    exit();
}

/* ADD / EDIT */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id_soal'] ?? '';
    $pertanyaan = clean($_POST['pertanyaan']);
    $a = clean($_POST['pilihan_a']);
    $b = clean($_POST['pilihan_b']);
    $c = clean($_POST['pilihan_c']);
    $d = clean($_POST['pilihan_d']);
    $jawaban = clean($_POST['jawaban_benar']);

    if ($id) {
        mysqli_query($conn, "UPDATE soal SET
            pertanyaan='$pertanyaan',
            pilihan_a='$a', pilihan_b='$b',
            pilihan_c='$c', pilihan_d='$d',
            jawaban_benar='$jawaban'
            WHERE id_soal='$id'");
    } else {
        mysqli_query($conn, "INSERT INTO soal
            (pertanyaan,pilihan_a,pilihan_b,pilihan_c,pilihan_d,jawaban_benar)
            VALUES
            ('$pertanyaan','$a','$b','$c','$d','$jawaban')");
    }
    header('Location: soal.php?msg=success');
    exit();
}

/* EDIT DATA */
$edit = null;
if (isset($_GET['edit'])) {
    $id = clean($_GET['edit']);
    $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM soal WHERE id_soal='$id'"));
}

/* DATA */
$soal = mysqli_query($conn, "SELECT * FROM soal ORDER BY id_soal DESC");
$page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Soal</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background: #f4f6fb;
        }

       .wrapper{
    min-height:100vh;
}

/* ===== SIDEBAR ===== */
.sidebar{
    position:fixed;
    top:0;
    left:0;
    width:230px;
    height:100vh;
    background:#1f2a6d;
    color:#fff;
    padding:20px;
    display:flex;
    flex-direction:column;
}
.sidebar h2{
    margin-bottom:30px;
}
.sidebar ul{
    list-style:none;
}
.sidebar ul li a{
    display:block;
    padding:12px;
    margin-bottom:6px;
    color:#fff;
    text-decoration:none;
    border-radius:8px;
}
.sidebar ul li a:hover,
.sidebar ul li.active a{
    background:#ffffff33;
}

        .logout {
            margin-top: auto;
            background: #fff;
            color: #1f2a6d;
            padding: 10px;
            border-radius: 20px;
            text-align: center;
            text-decoration: none;
            font-weight: bold;
        }

        /* MAIN */
       .main{
    margin-left:230px;
    min-height:100vh;
    display:flex;
    flex-direction:column;
}

/* ===== TOPBAR ===== */
.topbar{
    background:#f1f1f1;
    padding:15px 25px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

/* ===== CONTENT ===== */
.content{
    flex:1;
    padding:25px;
    overflow-y:auto;
}

        .card {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .card h3 {
            background: #1f2a6d;
            color: #fff;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        /* FORM */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        textarea {
            grid-column: span 2;
        }

        button {
            background: #1f2a6d;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 8px;
            margin-top: 10px;
            cursor: pointer;
        }

        /* SOAL */
        .soal-item {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .soal-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 15px;
        }

        .soal-content {
            flex: 1;
        }

        .soal-action {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .badge {
            background: #e3f2fd;
            color: #1976d2;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 12px;
        }

        .btn-warning,
        .btn-danger {
            padding: 8px 12px;
            border-radius: 8px;
            text-decoration: none;
            color: #fff;
            font-size: 14px;
            text-align: center;
        }

        .btn-warning {
            background: #f0ad4e;
        }

        .btn-danger {
            background: #d9534f;
        }
    </style>
</head>

<body>
    <div class="wrapper">

        <!-- SIDEBAR -->
        <aside class="sidebar">
            <h2>Menu</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li class="<?= $page == 'soal.php' ? 'active' : '' ?>"><a href="soal.php">Kelola Soal Tes</a></li>
                <li><a href="list_pendaftar.php">List Pendaftaran</a></li>
                <li><a href="hasil_test.php">Hasil Tes</a></li>
                <li><a href="daftar_ulang.php">Data Daftar Ulang</a></li>
            </ul>
            <a href="logout.php" class="logout">Log out</a>
        </aside>

        <!-- MAIN -->
        <main class="main">
            <header class="topbar">
                <div><strong> Kelola Soal Tes</strong></div>
                <div>Hi,
                    <?= $_SESSION['admin_name'] ?>
                </div>
            </header>

            <section class="content">

                <?php if (isset($_GET['msg'])): ?>
                    <div class="card">
                        <?= $_GET['msg'] == 'success' ? '✅ Data tersimpan' : '🗑 Data dihapus' ?>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <h3>
                        <?= $edit ? 'Edit Soal' : 'Tambah Soal' ?>
                    </h3>
                    <form method="POST">
                        <?php if ($edit): ?>
                            <input type="hidden" name="id_soal" value="<?= $edit['id_soal'] ?>">
                        <?php endif; ?>

                        <textarea name="pertanyaan" required><?= $edit['pertanyaan'] ?? '' ?></textarea>

                        <div class="form-grid">
                            <input name="pilihan_a" placeholder="Pilihan A" required
                                value="<?= $edit['pilihan_a'] ?? '' ?>">
                            <input name="pilihan_b" placeholder="Pilihan B" required
                                value="<?= $edit['pilihan_b'] ?? '' ?>">
                            <input name="pilihan_c" placeholder="Pilihan C" required
                                value="<?= $edit['pilihan_c'] ?? '' ?>">
                            <input name="pilihan_d" placeholder="Pilihan D" required
                                value="<?= $edit['pilihan_d'] ?? '' ?>">
                        </div>

                        <div class="form-grid">
                            <select name="jawaban_benar" required>
                                <option value="">Jawaban</option>
                                <?php foreach (['a', 'b', 'c', 'd'] as $j): ?>
                                    <option value="<?= $j ?>" <?= ($edit && $edit['jawaban_benar'] == $j) ? 'selected' : '' ?>>
                                        <?= strtoupper($j) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button>
                            <?= $edit ? 'Update' : 'Simpan' ?>
                        </button>
                        <?php if ($edit): ?>
                            <a href="soal.php" style="padding:10px;text-decoration:none">Batal</a>
                        <?php endif; ?>
                    </form>
                </div>

                <div class="card">
                    <h3>Daftar Soal</h3>
                    <?php $no = 1;
                    while ($r = mysqli_fetch_assoc($soal)): ?>
                        <div class="soal-item">
                            <div class="soal-row">
                                <div class="soal-content">
                                    <b>
                                        <?= $no++ ?>.
                                        <?= $r['pertanyaan'] ?>
                                    </b><br>
                                    <p>A.
                                        <?= $r['pilihan_a'] ?>
                                    </p>
                                    <p>B.
                                        <?= $r['pilihan_b'] ?>
                                    </p>
                                    <p>C.
                                        <?= $r['pilihan_c'] ?>
                                    </p>
                                    <p>D.
                                        <?= $r['pilihan_d'] ?>
                                    </p>
                                    <p><b>Jawaban:
                                            <?= strtoupper($r['jawaban_benar']) ?>
                                        </b></p>
                                </div>

                                <div class="soal-action">
                                    <a href="?edit=<?= $r['id_soal'] ?>" class="btn-warning">Edit</a>
                                    <a href="?delete=<?= $r['id_soal'] ?>" class="btn-danger"
                                        onclick="return confirm('Hapus soal ini?')">Hapus</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

            </section>
        </main>
    </div>
</body>

</html>
<?php
require_once '../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT * FROM calon_mahasiswa WHERE id_calon='$user_id'"
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

if (!$biodata_lengkap) {
    $_SESSION['alert'] = 'Lengkapi biodata Anda terlebih dahulu sebelum mengikuti tes!';
    header('Location: profil.php');
    exit();
}

if ($user['status_test'] != 'belum_test') {
    header('Location: dashboard.php');
    exit();
}


$nomor_valid = false;
$error_nomor = '';

if (isset($_POST['cek_nomor'])) {
    $nomor_input = clean($_POST['nomor_test']);

    if ($nomor_input === $user['nomor_test']) {
        $_SESSION['nomor_test_ok'] = true;
        $nomor_valid = true;
    } else {
        $error_nomor = 'Nomor tes tidak valid!';
    }
}

if (isset($_SESSION['nomor_test_ok'])) {
    $nomor_valid = true;
}


if (isset($_POST['submit_test']) && isset($_SESSION['nomor_test_ok'])) {

    $soal = mysqli_query($conn, "SELECT * FROM soal");
    $total = mysqli_num_rows($soal);
    $benar = 0;

    mysqli_query($conn, "DELETE FROM hasil_test WHERE id_calon='$user_id'");

    while ($s = mysqli_fetch_assoc($soal)) {
        $jawaban = strtoupper($_POST['jawaban_' . $s['id_soal']] ?? '');
        $is_benar = ($jawaban == $s['jawaban_benar']) ? 1 : 0;
        if ($is_benar)
            $benar++;

        if (!empty($jawaban)) {
            mysqli_query(
                $conn,
                "INSERT INTO hasil_test (id_calon,id_soal,jawaban)
                 VALUES ('$user_id','{$s['id_soal']}','$jawaban')"
            );
        }
    }

    $nilai = ($benar / $total) * 100;
    $status = ($nilai >= 60) ? 'lulus' : 'tidak_lulus';

    mysqli_query(
        $conn,
        "UPDATE calon_mahasiswa
         SET nilai_test='$nilai', status_test='$status'
         WHERE id_calon='$user_id'"
    );

    unset($_SESSION['nomor_test_ok']);
    header('Location: hasil_test.php');
    exit();
}

$soal_list = mysqli_query($conn, "SELECT * FROM soal ORDER BY id_soal ASC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tes Masuk Mahasiswa Baru</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f3f4f6
        }

        /* HEADER */
        .header {
            background: #1f2344;
            color: #fff;
            padding: 12px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header h1 {
            font-size: 18px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .header a, .btn-back {
            background: rgba(255,255,255,0.2);
            color: #fff;
            padding: 8px 20px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items:center;
            gap: 8px;
            font-size: 14px;
            transition: 0.2s;
        }

        .header a:hover, .btn-back:hover {
            background: rgba(255,255,255,0.3);
        }

        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px
        }

        /* ======================
       NOMOR TES (CENTER)
    ====================== */
        .nomor-test-box {
            max-width: 600px;
            margin: 120px auto;
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .nomor-test-box h2 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #1c1e21;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .nomor-test-box h2 i {
            color: #4267B2;
        }

        .nomor-test-box p {
            color: #65676b;
            margin-bottom: 25px;
            line-height: 1.5;
        }

        .nomor-test-box input {
            width: 80%;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            text-align: center;
            font-size: 15px;
            transition: 0.2s;
        }

        .nomor-test-box input:focus {
            outline: none;
            border-color: #4267B2;
        }

        .nomor-test-box button {
            margin-top: 25px;
            background: #4267B2;
            color: #fff;
            border: none;
            padding: 12px 36px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .nomor-test-box button:hover {
            background: #365899;
        }

        /* ======================
   PETUNJUK + TIMER
====================== */
        .info {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .info h2 {
            color: #1c1e21;
            font-size: 18px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info h2 i {
            color: #4267B2;
        }

        .info ul {
            padding-left: 20px;
            line-height: 1.8;
            color: #1c1e21;
        }

        .time-box {
            background: linear-gradient(135deg, #4267B2, #5578c7);
            color: #fff;
            padding: 20px 25px;
            border-radius: 8px;
            font-weight: 700;
            text-align: center;
            min-width: 140px;
        }

        .time-box i {
            font-size: 24px;
            margin-bottom: 10px;
            display: block;
        }

        /* ======================
   SOAL
====================== */
        .question {
            background: #fff;
            padding: 24px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .question.unanswered {
            border: 1px solid #fca5a5;
            background: #fff5f5;
        }

        .question h3 {
            color: #1c1e21;
            margin-bottom: 18px;
            font-size: 16px;
            line-height: 1.5;
        }

        .options {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .options label {
            display: flex;
            gap: 12px;
            cursor: pointer;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            transition: 0.2s;
            align-items: flex-start;
        }

        .options label:hover {
            border-color: #4267B2;
            background: #f0f4ff;
        }

        .options input[type="radio"] {
            margin-top: 2px;
            cursor: pointer;
        }

        /* SUBMIT */
        .submit {
            text-align: center;
            margin: 40px 0;
            position: relative;
            z-index: 1;
        }

        .submit button {
            background: #28a745;
            color: white;
            border: none;
            padding: 14px 50px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            position: relative;
            z-index: 2;
        }

        .submit button:hover {
            background: #218838;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1><i class="fa-solid fa-file-pen"></i> Tes Masuk Mahasiswa Baru</h1>
        <a href="dashboard.php" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>

    <div class="container">

        <?php if (!$nomor_valid): ?>
            <form method="POST">
                <div class="nomor-test-box">
                    <h2><i class="fa-solid fa-lock"></i> Masukkan Nomor Tes</h2>
                    <p>Nomor tes wajib di isi sebelum mengerjakan soal. Nomor tes dapat dilihat di dashboard.</p>

                    <input type="text" name="nomor_test" required placeholder="Masukkan nomor tes">

                    <?php if ($error_nomor): ?>
                        <p style="color:#dc3545;margin-top:15px;font-weight:600"><i class="fa-solid fa-circle-xmark"></i> <?= $error_nomor ?></p>
                    <?php endif; ?>

                    <button type="submit" name="cek_nomor">
                        <i class="fa-solid fa-right-to-bracket"></i> MASUK TES
                    </button>
                </div>
            </form>
        <?php endif; ?>

        <?php if ($nomor_valid): ?>
            <div class="info">
                <div>
                    <h2><i class="fa-solid fa-clipboard-list"></i> Petunjuk Tes</h2>
                    <ul>
                        <li>Bacalah setiap soal dengan teliti</li>
                        <li>Pilih satu jawaban paling tepat</li>
                        <li>Waktu pengerjaan: 30 menit</li>
                        <li>Nilai minimum kelulusan: 60</li>
                        <li>Pastikan semua soal dijawab</li>
                    </ul>
                </div>
                <div class="time-box">
                    <i class="fa-solid fa-clock"></i>
                    <div>Sisa Waktu</div>
                    <div style="font-size: 24px; margin-top: 8px;"><span id="timer">30:00</span></div>
                </div>
            </div>

            <form method="POST" id="testForm">
                <?php $no = 1;
                while ($s = mysqli_fetch_assoc($soal_list)): ?>
                    <div class="question">
                        <h3><?= $no ?>. <?= htmlspecialchars($s['pertanyaan']) ?></h3>
                        <div class="options">
                            <label>
                                <input type="radio" name="jawaban_<?= $s['id_soal'] ?>" value="a">
                                <span><strong>A.</strong> <?= htmlspecialchars($s['pilihan_a']) ?></span>
                            </label>
                            <label>
                                <input type="radio" name="jawaban_<?= $s['id_soal'] ?>" value="b">
                                <span><strong>B.</strong> <?= htmlspecialchars($s['pilihan_b']) ?></span>
                            </label>
                            <label>
                                <input type="radio" name="jawaban_<?= $s['id_soal'] ?>" value="c">
                                <span><strong>C.</strong> <?= htmlspecialchars($s['pilihan_c']) ?></span>
                            </label>
                            <label>
                                <input type="radio" name="jawaban_<?= $s['id_soal'] ?>" value="d">
                                <span><strong>D.</strong> <?= htmlspecialchars($s['pilihan_d']) ?></span>
                            </label>
                        </div>
                    </div>
                    <?php $no++; endwhile; ?>

                <div class="submit">
                    <button type="submit" name="submit_test">
                        <i> KIRIM JAWABAN</i> 
                    </button>
                </div>
            </form>
        <?php endif; ?>

    </div>

    <?php if ($nomor_valid): ?>
        <script>
            let time = 1800;
            setInterval(() => {
                let m = Math.floor(time / 60);
                let s = time % 60;
                document.getElementById('timer').innerText =
                    m.toString().padStart(2, '0') + ':' + s.toString().padStart(2, '0');
                if (time <= 0) {
                    alert('Waktu habis, jawaban dikirim otomatis');
                    document.getElementById('testForm').submit();
                }
                time--;
            }, 1000);

            const testForm = document.getElementById('testForm');
            testForm.addEventListener('submit', (event) => {
                const questions = document.querySelectorAll('.question');
                let firstMissing = null;

                questions.forEach((question) => {
                    const checked = question.querySelector('input[type="radio"]:checked');
                    if (!checked) {
                        question.classList.add('unanswered');
                        if (!firstMissing) {
                            firstMissing = question;
                        }
                    } else {
                        question.classList.remove('unanswered');
                    }
                });

                if (firstMissing) {
                    event.preventDefault();
                    alert('Masih ada soal yang belum dijawab.');
                    firstMissing.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return;
                }

                if (!confirm('Yakin ingin mengirim jawaban? Anda tidak dapat mengerjakan tes lagi!')) {
                    event.preventDefault();
                }
            });
        </script>
    <?php endif; ?>

</body>

</html>
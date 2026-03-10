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

// Hapus data calon mahasiswa
mysqli_query(
    $conn,
    "DELETE FROM calon_mahasiswa WHERE id_calon = '$id'"
);

// Kembali ke halaman sebelumnya
$referer = isset($_GET['from']) ? $_GET['from'] : 'hasil_test.php';
header('Location: ' . $referer);
exit();

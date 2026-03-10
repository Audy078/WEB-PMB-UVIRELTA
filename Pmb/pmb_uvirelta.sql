-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 10 Mar 2026 pada 17.10
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pmb_uvirelta`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`, `nama_lengkap`, `created_at`) VALUES
(1, 'admin', 'admin123', 'Administrator PMB', '2026-02-12 19:09:20');

-- --------------------------------------------------------

--
-- Struktur dari tabel `calon_mahasiswa`
--

CREATE TABLE `calon_mahasiswa` (
  `id_calon` int(11) NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `asal_sekolah` varchar(255) NOT NULL,
  `jurusan_pilihan` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `nik` varchar(16) DEFAULT NULL,
  `tempat_lahir` varchar(100) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') DEFAULT NULL,
  `nomor_orang_tua` varchar(20) DEFAULT NULL,
  `nomor_wa` varchar(20) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `nomor_test` varchar(50) DEFAULT NULL,
  `status_test` enum('belum_test','sudah_test','lulus','tidak_lulus') DEFAULT 'belum_test',
  `nilai_test` int(11) DEFAULT NULL,
  `status_daftar_ulang` enum('belum','sudah') DEFAULT 'belum',
  `nim` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `hasil_test`
--

CREATE TABLE `hasil_test` (
  `id_hasil` int(11) NOT NULL,
  `id_calon` int(11) NOT NULL,
  `id_soal` int(11) NOT NULL,
  `jawaban` enum('A','B','C','D') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `soal`
--

CREATE TABLE `soal` (
  `id_soal` int(11) NOT NULL,
  `pertanyaan` text NOT NULL,
  `pilihan_a` varchar(255) NOT NULL,
  `pilihan_b` varchar(255) NOT NULL,
  `pilihan_c` varchar(255) NOT NULL,
  `pilihan_d` varchar(255) NOT NULL,
  `jawaban_benar` enum('A','B','C','D') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `soal`
--

INSERT INTO `soal` (`id_soal`, `pertanyaan`, `pilihan_a`, `pilihan_b`, `pilihan_c`, `pilihan_d`, `jawaban_benar`, `created_at`) VALUES
(1, 'Berapakah hasil dari 15 + 27?', '40', '41', '42', '43', 'C', '2026-02-12 19:09:20'),
(2, 'Berapakah hasil dari 144 ÷ 12?', '10', '11', '12', '13', 'C', '2026-02-12 19:09:20'),
(3, 'Ibu kota negara Indonesia adalah...', 'Bandung', 'Surabaya', 'Jakarta', 'Medan', 'C', '2026-02-12 19:09:20'),
(4, 'Kata baku yang benar adalah...', 'Sistim', 'System', 'Sistem', 'Sisitem', 'C', '2026-02-12 19:09:20'),
(5, 'Proklamasi kemerdekaan Indonesia terjadi pada tanggal...', '16 Agustus 1945', '17 Agustus 1945', '18 Agustus 1945', '19 Agustus 1945', 'B', '2026-02-12 19:09:20'),
(6, 'Presiden pertama Republik Indonesia adalah...', 'Soeharto', 'Soekarno', 'BJ Habibie', 'Megawati', 'B', '2026-02-12 19:09:20'),
(7, 'Planet terbesar dalam tata surya adalah...', 'Mars', 'Jupiter', 'Saturnus', 'Uranus', 'B', '2026-02-12 19:09:20'),
(8, 'Antonim dari kata \"rajin\" adalah...', 'Tekun', 'Giat', 'Malas', 'Semangat', 'C', '2026-02-12 19:09:20'),
(9, 'What is the capital city of England?', 'Manchester', 'Liverpool', 'London', 'Birmingham', 'C', '2026-02-12 19:09:20'),
(10, 'Berapakah 20% dari 500?', '80', '90', '100', '110', 'C', '2026-02-12 19:09:20'),
(12, 'hbdcnjsxkmfndm', 'graeh', 'tes', 'llkjhgf', 'tes', 'A', '2026-03-05 00:37:02');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_username` (`username`);

--
-- Indeks untuk tabel `calon_mahasiswa`
--
ALTER TABLE `calon_mahasiswa`
  ADD PRIMARY KEY (`id_calon`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_nomor_test` (`nomor_test`),
  ADD KEY `idx_status_test` (`status_test`);

--
-- Indeks untuk tabel `hasil_test`
--
ALTER TABLE `hasil_test`
  ADD PRIMARY KEY (`id_hasil`),
  ADD KEY `idx_id_calon` (`id_calon`),
  ADD KEY `idx_id_soal` (`id_soal`);

--
-- Indeks untuk tabel `soal`
--
ALTER TABLE `soal`
  ADD PRIMARY KEY (`id_soal`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `calon_mahasiswa`
--
ALTER TABLE `calon_mahasiswa`
  MODIFY `id_calon` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `hasil_test`
--
ALTER TABLE `hasil_test`
  MODIFY `id_hasil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- AUTO_INCREMENT untuk tabel `soal`
--
ALTER TABLE `soal`
  MODIFY `id_soal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `hasil_test`
--
ALTER TABLE `hasil_test`
  ADD CONSTRAINT `hasil_test_ibfk_1` FOREIGN KEY (`id_calon`) REFERENCES `calon_mahasiswa` (`id_calon`) ON DELETE CASCADE,
  ADD CONSTRAINT `hasil_test_ibfk_2` FOREIGN KEY (`id_soal`) REFERENCES `soal` (`id_soal`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

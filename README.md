# Sistem PMB Universitas Virelta Indonesia

Website **Penerimaan Mahasiswa Baru (PMB)** berbasis **PHP Native** dan **MySQL** untuk membantu proses pendaftaran calon mahasiswa secara online.

Sistem memiliki dua aktor utama, yaitu:
- **Calon Mahasiswa / User**
- **Admin**

## Fitur Utama

### Fitur Calon Mahasiswa
- Registrasi akun PMB
- Login user
- Melengkapi biodata diri
- Upload foto profil
- Mengikuti tes masuk online
- Validasi nomor tes sebelum ujian
- Melihat hasil tes
- Melakukan daftar ulang jika lulus
- Mencetak dokumen / bukti daftar ulang
- Mencetak kartu mahasiswa

### Fitur Admin
- Login admin 
- Dashboard statistik PMB
- Melihat total pendaftar
- Melihat jumlah peserta lulus
- Melihat jumlah peserta daftar ulang
- Mengelola soal tes
- Melihat daftar pendaftar
- Mengedit data pendaftar
- Melihat hasil tes peserta
- Mengedit hasil tes
- Mengelola data daftar ulang
- Melihat detail mahasiswa yang sudah daftar ulang

## Alur Sistem

### Alur User / Mahasiswa
1. User melakukan registrasi akun
2. User login ke sistem
3. User melengkapi biodata
4. User mengikuti tes masuk
5. Sistem menghitung nilai tes
6. User melihat hasil tes
7. Jika lulus, user melakukan daftar ulang
8. User mencetak bukti daftar ulang / kartu mahasiswa

### Alur Admin
1. Admin login
   *username* : admin
   *password* : admin123
3. Admin masuk ke dashboard
4. Admin mengelola soal tes
5. Admin memantau data pendaftar
6. Admin memantau hasil tes
7. Admin memantau data daftar ulang

## Tools yang Digunakan
- **Frontend:** HTML, CSS, JavaScript
- **Backend:** PHP Native
- **Database:** MySQL / MariaDB
- **Server:** XAMPP
- **Icon Library:** Font Awesome

## Struktur Folder

```bash
Pmb/
├── admin/
├── user/
├── assets/
├── icons/
├── uploads/
├── config.php
├── index.php
```

## Struktur Modul

### Halaman Utama
- `index.php` → Landing page website PMB
- `config.php` → Konfigurasi database dan session

### Modul User
- `user/register.php` → Registrasi akun
- `user/login.php` → Login user
- `user/dashboard.php` → Dashboard user
- `user/profil.php` → Biodata user
- `user/test.php` → Tes masuk online
- `user/hasil_test.php` → Hasil tes
- `user/daftar_ulang.php` → Form daftar ulang
- `user/kartu_mahasiswa.php` → Cetak dokumen / kartu

### Modul Admin
- `admin/login.php` → Login admin
- `admin/dashboard.php` → Dashboard admin
- `admin/kelola_soal.php` → CRUD soal tes
- `admin/list_pendaftar.php` → Daftar calon mahasiswa
- `admin/edit_pendaftar.php` → Edit data pendaftar
- `admin/hasil_test.php` → Data hasil tes
- `admin/edit_hasil_test.php` → Edit hasil tes
- `admin/daftar_ulang.php` → Data daftar ulang mahasiswa

## Folder assets 
- untuk logo dan gambar website

## Folder uploads
- habis upload foto masuknya ke folder uploads
## Database
Sistem menggunakan database **pmb_uvirelta**.

Tabel utama yang digunakan:
- `calon_mahasiswa`
- `admin`
- `soal`
- `hasil_test`




# HL Sales & Receivables Management System

Aplikasi internal berbasis web untuk mengelola transaksi penjualan (bon), pemantauan piutang, pelacakan akumulasi bonus pelanggan (*cash basis*), laba bersih, serta rekapitulasi data keuangan. Sistem ini dibangun menggunakan **Laravel 11**, **Laravel Livewire v3**, dan **Tabler.io Bootstrap 5** untuk antarmuka pengguna (UI) yang modern dan responsif.

> [!IMPORTANT]
> **Kredensial Login Default (Seeded Admin):**
> * **Email / Username**: `admin@hlfinance.com`
> * **Password**: `adminfinance`

---

## 🛠️ Tech Stack & Dependensi
* **Core Framework**: Laravel 11 & PHP >= 8.2
* **Frontend Library**: Laravel Livewire v3 & Tabler.io Core (Bootstrap 5)
* **Icons**: Tabler Icons
* **Database**: SQLite (Bawaan & Praktis)
* **Grafik**: Chart.js
* **PDF Exporter**: Barryvdh Laravel DomPDF

---

## 💻 Panduan Instalasi Lokal (Development)

### 1. Prasyarat
Pastikan komputer Anda sudah terinstal:
* PHP >= 8.2 (dengan ekstensi `pdo_sqlite` aktif)
* Composer
* Git

### 2. Langkah Instalasi
1. Clone repositori ini ke komputer Anda:
   ```bash
   git clone https://github.com/scripted42/HL-apps.git
   cd HL-apps
   ```
2. Instal dependensi PHP:
   ```bash
   composer install
   ```
3. Salin konfigurasi environment:
   ```bash
   copy .env.example .env
   ```
4. Generate key aplikasi:
   ```bash
   php artisan key:generate
   ```
5. Buat file database SQLite kosong di folder database:
   * **Windows (PowerShell)**:
     ```powershell
     New-Item -Path database -Name database.sqlite -ItemType File
     ```
   * **Linux / Mac**:
     ```bash
     touch database/database.sqlite
     ```
6. Jalankan migrasi tabel beserta pengisian data awal (*seeding*):
   ```bash
   php artisan migrate:fresh --seed
   ```
7. Buat tautan storage publik:
   ```bash
   php artisan storage:link
   ```
8. Jalankan server lokal:
   ```bash
   php artisan serve
   ```
9. Buka di browser: **[http://127.0.0.1:8000](http://127.0.0.1:8000)**
   * **Email Login**: `admin@hlfinance.com`
   * **Password**: `adminfinance`

---

## 🚀 Panduan Detail Deployment di aaPanel & Cloudflare Tunnel

Ikuti panduan berikut agar proses instalasi pada server produksi berjalan lancar tanpa mengalami masalah hak akses (*permissions*), folder tidak kosong, atau masalah kompilasi cache.

### Fase 1: Persiapan Environment PHP di aaPanel
1. Masuk ke dashboard aaPanel -> **App Store**.
2. Masuk ke setelan **PHP (8.2 / 8.3)** yang digunakan:
   * Di tab **Install Extensions**, pastikan ekstensi `fileinfo` dan `sqlite3` (atau `pdo_sqlite`) sudah terinstal.
   * Di tab **Disabled Functions**, pastikan fungsi **`symlink`** dan **`putenv`** dihapus dari daftar blokir.
3. Buat website baru di aaPanel dengan memasukkan domain tujuan Anda (misal: `hlfinance.ahlinyacuan.pro`). Pilih opsi database *Do not create* jika ingin menggunakan SQLite bawaan.

---

### Fase 2: Pengunduhan Kode (Bypass Folder Tidak Kosong)
aaPanel secara otomatis membuat beberapa file default seperti `index.html` dan `.user.ini` yang terkunci. Agar `git clone` berhasil tanpa error *destination path already exists*:

Hubungkan ke terminal server Anda melalui SSH dan jalankan perintah ini:
```bash
cd /www/wwwroot/hlfinance.ahlinyacuan.pro

# 1. Clone repositori ke folder sementara
sudo git clone https://github.com/scripted42/HL-apps.git temp_clone

# 2. Pindahkan semua file proyek (termasuk berkas tersembunyi .git/ & .env) ke direktori utama
sudo mv temp_clone/* .
sudo mv temp_clone/.* . 2>/dev/null

# 3. Hapus folder sementara
sudo rm -rf temp_clone
```

---

### Fase 3: Pembuatan Folder Cache & Perizinan (Mengatasi "Valid Cache Path" Error)
Laravel membutuhkan folder internal untuk menyusun file cache/session/view. Buat folder-folder ini terlebih dahulu agar script pengoptimalan Composer tidak gagal:

```bash
# 1. Buat folder penyimpanan cache Laravel
sudo -u www mkdir -p storage/framework/cache/data
sudo -u www mkdir -p storage/framework/sessions
sudo -u www mkdir -p storage/framework/views
sudo -u www mkdir -p storage/app/public

# 2. Buat file database SQLite kosong
sudo -u www touch database/database.sqlite
```

---

### Fase 4: Instalasi Dependensi & Setup Laravel
Jalankan semua perintah berikut menggunakan identitas user **`www`** (user web server aaPanel) untuk mencegah masalah izin menulis berkas (*write permissions*):

```bash
# 1. Daftarkan direktori ini sebagai safe directory di git
git config --global --add safe.directory /www/wwwroot/hlfinance.ahlinyacuan.pro

# 2. Jalankan instalasi composer menggunakan user www
sudo -u www composer install --no-dev --optimize-autoloader

# 3. Salin file konfigurasi .env
sudo -u www cp .env.example .env

# 4. Generate application key
sudo -u www php artisan key:generate
```

Buka file `.env` lewat File Manager aaPanel dan ubah koneksi database menjadi SQLite:
```env
DB_CONNECTION=sqlite
# Pastikan untuk menghapus atau memberi tanda komentar (#) pada DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
```

Lanjutkan konfigurasi database & tautan aset di terminal:
```bash
# 5. Jalankan migrasi dan isi database awal
sudo -u www php artisan migrate --force --seed

# 6. Buat storage link
sudo -u www php artisan storage:link

# 7. Pastikan perizinan folder storage dan bootstrap sudah benar
sudo chattr -i .user.ini
sudo chown -R www:www /www/wwwroot/hlfinance.ahlinyacuan.pro
sudo chattr +i .user.ini
```

---

### Fase 5: Setel Web Server Nginx di aaPanel
1. Di aaPanel, masuk ke menu **Website** -> Klik nama domain Anda -> Buka **Settings**.
2. **Site Directory**:
   * Setel **Running directory** ke `/public` lalu klik **Save**.
3. **URL Rewrite**:
   * Pilih template **laravel** dari pilihan dropdown, lalu klik **Save**.

---

### Fase 6: Konfigurasi Cloudflare Tunnel (Zero Trust)
Buka portal **Cloudflare Zero Trust** dan tambahkan Public Hostname untuk Tunnel Anda:
1. **Public Hostname**:
   * **Subdomain**: `hlfinance`
   * **Domain**: `ahlinyacuan.pro`
   * **Type**: `HTTP`
   * **URL**: `localhost` (atau `localhost:80`)
2. **HTTP Host Header (Sangat Penting)**:
   * Gulir ke bawah, klik **Additional Application Settings** -> **HTTP Settings**.
   * Di kolom **HTTP Host Header**, masukkan domain lengkap website aaPanel Anda:
     ```text
     hlfinance.ahlinyacuan.pro
     ```
   * Klik **Save Hostname**.
3. Tambahkan DNS `CNAME` baru di dashboard DNS Cloudflare utama Anda untuk domain `ahlinyacuan.pro` yang mengarah ke `[ID-TUNNEL-ANDA].cfargotunnel.com` jika tidak terbuat secara otomatis.

---

## 🧪 Pengujian & Verifikasi
Seluruh fungsionalitas logika bisnis, penghitungan diskon bertingkat, dan penghitungan bonus diproteksi oleh automated testing.

Untuk menjalankan pengujian secara lokal, gunakan perintah:
```bash
php artisan test
```

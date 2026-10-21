# Aplikasi Travel Booking (Web Service)

Aplikasi pemesanan tiket travel berbasis web yang dibangun untuk memenuhi syarat Uji Kompetensi Pemrograman. Aplikasi ini menggunakan **PHP Native (Tanpa Framework)** dengan konsep **Object-Oriented Programming (OOP)**, dan pola desain **MVC (Model-View-Controller)** sederhana.

## Fitur Utama

### 1. Multi-Level User (Role)
- **Admin**: Akses penuh ke seluruh sistem (Layanan, Kendaraan, User, Transaksi, Laporan).
- **Kasir**: Fokus pada manajemen transaksi pembayaran dan cetak invoice.
- **Pelanggan**: Dapat mencari layanan, melihat stok (tersedia), melakukan booking, dan melihat riwayat/invoice sendiri.

### 2. Manajemen Layanan & Kendaraan
- Layanan perjalanan terintegrasi dengan armada kendaraan.
- Stok tiket otomatis menyesuaikan dengan kapasitas kendaraan yang dipilih.
- Validasi ketersediaan kendaraan sebelum membuat layanan.

### 3. Transaksi & Invoice
- Alur pemesanan: Booking -> Pending -> Pembayaran (oleh Kasir/Admin) -> Lunas.
- Invoice otomatis ter-generate dalam format siap cetak (PDF/Print) hanya untuk transaksi yang lunas.
- Keamanan: Pelanggan hanya bisa melihat invoice miliknya sendiri.

### 4. Teknis & Struktur Data
- **OOP Patterns**: Menggunakan Interface (`ICrud`), Singleton (`Database Connection`), dan Inheritance (`BaseModel`).
- **Custom Data Structure**: Hasil query database dipetakan ke dalam Objek/Array kustom (`stdClass`) sebelum dikirim ke View, tidak menggunakan raw array dari PDO secara langsung di View.
- **Security**: Password hashing (Bcrypt), Prepared Statements (SQL Injection Protection), Session Management, Role Middleware.

## Struktur Folder

```
/travel_booking
|-- /app                  # Core Logic Aplikasi
|   |-- /Classes          # Model & Bisnis Logic (User, Layanan, Kendaraan, dll)
|   |-- /Config           # Konfigurasi (Database.php)
|   |-- /Interfaces       # Interface Contracts (ICrud.php)
|   |-- /Lib              # Helper Libraries (Invoice.php)
|   |-- /Views            # Tampilan Antarmuka (HTML/PHP)
|       |-- /booking      # View untuk fitur booking pelanggan
|       |-- /kendaraan    # View manajemen kendaraan
|       |-- /layanan      # View manajemen layanan
|       |-- /transaksi    # View kasir/laporan
|       |-- /user         # View manajemen user
|-- /public               # Asset Publik
|   |-- /css              # File CSS (Style.css)
|-- index.php             # Router / Entry Point Utama
|-- migrate.php           # Script helper untuk database migration
|-- database.sql          # File Import Database MySQL
|-- README.md             # Dokumentasi Proyek
```

## Persyaratan Sistem & Instalasi

1.  **Web Server**: XAMPP / Laragon (Apache + MySQL).
2.  **PHP Versi**: 7.4 atau 8.x.
3.  **Database**:
    - Buat database baru bernama `db_travel_booking` di phpMyAdmin.
    - Import file `database.sql`.
4.  **Konfigurasi**:
    - Buka `app/Config/Database.php`.
    - Sesuaikan `$username` (default: root) dan `$password` (default: kosong).

## Cara Penggunaan

Buka browser dan akses: `http://localhost/travel_booking`

**Akun Demo:**
- **Admin**: `admin` / `password`
- **Kasir**: `kasir` / `password`
- **Pelanggan**: `pelanggan` / `password`


## Author
**Iqbal Habibi**
*Tanggal Pembuatan: 21 Januari 2026*
Dibuat untuk memenuhi tugas Uji Kompetensi Keahlian (UKK) Rekayasa Perangkat Lunak.

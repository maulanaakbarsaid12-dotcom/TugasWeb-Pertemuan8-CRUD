# CRUD Inventaris Barang

Tugas Rutin 8 — Pemrograman Web (RDBMS & Integrasi PHP-MySQL)

- **Nama:** Maulana Akbar Said Hasugian
- **NIM:** 4252550012
- **Kelas:** PSIK 25 A

Aplikasi CRUD inventaris barang memakai **PHP native + PDO + MySQL**. Koneksi database
memakai pola **Singleton**, semua query yang menerima input pengguna memakai
**prepared statements**, dan semua output disanitasi dengan `htmlspecialchars()`.

## Fitur

- **Read:** daftar produk dengan JOIN ke tabel `categories` dan `suppliers`
- **Create:** tambah produk (dropdown kategori dan supplier)
- **Update:** ubah produk (form terisi data lama)
- **Delete:** hapus produk (konfirmasi dan metode POST)
- Pesan sukses/gagal (flash message) memakai session
- Validasi input di sisi server dan penanganan error dengan `try-catch`

## Desain Database

![ERD](screenshots/erd.png)

```
categories (id PK, name)
suppliers  (id PK, name, phone)
products   (id PK, name, category_id FK, supplier_id FK, price, stock)
```

Relasi **one-to-many**: satu kategori memiliki banyak produk, dan satu supplier
memiliki banyak produk. Foreign key berada di sisi "many" (`products`).
Desain sudah memenuhi **3NF**: nama kategori dan supplier disimpan satu kali di
tabel masing-masing, dan `products` hanya menyimpan id-nya.

## Struktur Proyek

```
crud-inventaris/
├── assets/
│   └── style.css
├── config/
│   └── database.php     # koneksi PDO (Singleton)
├── screenshots/         # gambar untuk README
├── schema.sql           # DDL + data contoh
├── index.php            # READ
├── create.php           # CREATE
├── edit.php             # UPDATE
├── delete.php           # DELETE
└── README.md
```

## Cara Menjalankan

1. Install **Laragon** (atau XAMPP), lalu jalankan **Apache** dan **MySQL**.
2. Salin folder `crud-inventaris` ke `C:\laragon\www\` (Laragon) atau `htdocs` (XAMPP).
3. Import database, pilih salah satu cara:
   - **phpMyAdmin:** buat database `inventaris_db`, buka tab *Import*, pilih `schema.sql`, lalu klik *Import*.
   - **Terminal:** `mysql -u root < schema.sql`
4. Pastikan pengaturan di `config/database.php` sesuai: host `localhost`,
   database `inventaris_db`, user `root`, password kosong (bawaan Laragon).
5. Buka `http://localhost/crud-inventaris/` di browser.

## Keamanan

- **SQL Injection:** semua query dengan input pengguna memakai prepared statements
  (`prepare()` dan `execute([...])`). Id dari URL juga dipaksa menjadi angka dengan `(int)`.
- **XSS:** setiap output ke HTML memakai `htmlspecialchars()` atau di-cast `(int)`.
- **Hapus data:** hanya lewat metode POST dengan konfirmasi, bukan link GET.
- **Error database:** detail teknis hanya dicatat di log (`error_log()`),
  pengguna hanya melihat pesan umum.
- **Opsi PDO:** `ERRMODE_EXCEPTION`, `FETCH_ASSOC`, dan `EMULATE_PREPARES => false`.

## Screenshot

### Daftar produk
![Daftar produk](screenshots/index.png)

### Tambah produk
![Tambah produk](screenshots/create.png)

### Edit produk
![Edit produk](screenshots/edit.png)

### Validasi input
![Validasi input](screenshots/validasi.png)

### Konfirmasi hapus
![Konfirmasi hapus](screenshots/hapus.png)
# Dokumentasi Sistem Inventori

**Versi:** 1.0
**Terakhir Diperbarui:** 12 Juli 2026

---

## Daftar Isi

1. [Pendahuluan](#1-pendahuluan)
2. [Teknologi yang Digunakan](#2-teknologi-yang-digunakan)
3. [Akun Default](#3-akun-default)
4. [Menu Utama & Penjelasan](#4-menu-utama--penjelasan)
5. [Role & Hak Akses](#5-role--hak-akses)
6. [Alur Kerja Utama](#6-alur-kerja-utama)
7. [Fitur Dashboard](#7-fitur-dashboard)
8. [Fitur Export & Cetak](#8-fitur-export--cetak)
9. [Aturan Bisnis (Business Rules)](#9-aturan-bisnis-business-rules)
10. [Struktur Data (Simplified)](#10-struktur-data-simplified)
11. [FAQ (Pertanyaan Umum)](#11-faq-pertanyaan-umum)

---

## 1. Pendahuluan

### Apa Itu Aplikasi Ini?

Aplikasi **Sistem Inventori** adalah sebuah aplikasi berbasis web yang digunakan untuk **mencatat, mengelola, dan memantau stok barang** di dalam gudang atau toko. Aplikasi ini membantu Anda mengetahui:

- Berapa stok barang yang tersedia
- Barang mana saja yang sudah habis atau menipis
- Barang mana yang paling sering keluar (laris)
- Riwayat setiap transaksi masuk dan keluar barang
- Nilai total persediaan barang

### Siapa yang Menggunakan Aplikasi Ini?

| Pengguna | Kegunaan |
|----------|----------|
| **Pemilik Usaha / Manajer** | Melihat ringkasan stok, laporan, dan grafik di dashboard |
| **Admin Gudang** | Mengelola data produk, kategori, supplier, dan transaksi masuk/keluar |
| **Kasir / Staff** | Mencatat transaksi barang masuk dan keluar |

### Di mana Aplikasi Ini Diakses?

Aplikasi diakses melalui **browser** (Chrome, Firefox, Edge, Safari) dengan alamat:

```
http://localhost:8000/admin atau stokku.my.id
```

> Catatan: Alamat ini berlaku untuk penggunaan lokal (di komputer sendiri). Untuk akses dari jaringan lain, alamat akan berbeda sesuai pengaturan server.

---

## 2. Teknologi yang Digunakan

Bagian ini menjelaskan komponen teknis dari aplikasi secara sederhana.

### Komponen Utama

| Komponen | Keterangan |
|----------|------------|
| **Framework Aplikasi** | Laravel 12 — kerangka kerja PHP untuk membangun aplikasi web |
| **Panel Administrasi** | Filament 4.0 — antarmuka admin yang modern dan mudah digunakan |
| **Basis Data** | Mysql |
| **Bahasa Pemrograman** | PHP 8.2 dan JavaScript |
| **Tampilan** | Tailwind CSS 4 — desain antarmuka yang bersih dan responsif |

### Fitur Pendukung

| Fitur | Kegunaan |
|-------|----------|
| **Export Excel (.xlsx)** | Mengunduh laporan stok dalam format spreadsheet |
| **Cetak PDF** | Mencetak laporan stok dalam format dokumen PDF |
| **Grafik Interaktif** | Menampilkan data stok dalam bentuk grafik di dashboard |
| **Autentikasi & Role** | Sistem login dan pembagian hak akses per pengguna |

---

## 3. Akun Default

Setelah pertama kali dijalankan, aplikasi sudah memiliki akun default untuk login:

| Keterangan | Nilai |
|------------|-------|
| **Email** | `admin@mail.com` |
| **Password** | `admin1234` |

> **PENTING:** Segera ubah password setelah login pertama kali melalui menu **Pengaturan Akun** (klik ikon profil di pojok kanan atas > Pengaturan).

### Akun Tambahan (jika RoleSeeder dijalankan)

| Nama | Email | Password | Role |
|------|-------|----------|------|
| Admin User | `admin@mail.com` | `admin1234` | Super Admin |
| Staff Admin | `admin@inventory.web` | `admin1234` | Admin |
| Staff Kasir | `kasir@inventory.web` | `kasir1234` | Kasir |

---

## 4. Menu Utama & Penjelasan

Setelah login, Anda akan melihat panel administrasi dengan menu-menu di sebelah kiri (sidebar). Berikut penjelasan setiap menu:

### 4.1 Dashboard

**Icon:** Rumah

Halaman utama yang ditampilkan setelah login. Menampilkan **ringkasan seluruh kondisi inventori** dalam bentuk kartu statistik dan grafik. Lihat [Bagian 7: Fitur Dashboard](#7-fitur-dashboard) untuk penjelasan lengkap.

---

### 4.2 Master Data

Bagian ini berisi data-data dasar yang dibutuhkan oleh sistem.

#### 4.2.1 Produk

**Icon:** Tas Belanja

Menu untuk mengelola daftar semua barang/produk yang dijual atau disimpan di gudang.

**Data yang dicatat:**

| Field | Keterangan | Contoh |
|-------|------------|--------|
| Nama Produk | Nama barang | Beras Rojo Lele |
| SKU | Kode unik produk (singkatan) | BRS-RL-50KG |
| Kategori | Kelompok barang | Bahan Pokok |
| Satuan | Unit pengukuran | Kg, Liter, Pcs, Box |
| Harga Beli | Harga saat barang masuk dari supplier | Rp 50.000 |
| Harga Jual | Harga saat barang dijual ke pelanggan | Rp 60.000 |
| Stok | Jumlah barang yang tersedia saat ini | 100 |

**Yang bisa dilakukan:**
- Melihat daftar semua produk
- Menambah produk baru
- Mengubah data produk
- Menghapus produk
- Mencari produk berdasarkan nama atau SKU
- Mengurutkan produk berdasarkan stok, harga, atau nama

#### 4.2.2 Kategori

**Icon:** Label

Menu untuk mengelompokkan produk berdasarkan jenisnya.

**Contoh kategori:**
- Makanan
- Minuman
- Bahan Pokok
- Peralatan

**Yang bisa dilakukan:**
- Melihat daftar kategori
- Menambah kategori baru
- Mengubah nama kategori
- Menghapus kategori

#### 4.2.3 Supplier

**Icon:** Truk

Menu untuk mencatat data supplier (pihak yang menyuplai barang ke gudang Anda).

**Data yang dicatat:**

| Field | Keterangan | Contoh |
|-------|------------|--------|
| Nama | Nama supplier/penjual | Supplier A |
| Telepon | Nomor telepon yang bisa dihubungi | 08123456789 |
| Alamat | Alamat lengkap supplier | Jl. Merdeka No. 10, Jakarta |

**Yang bisa dilakukan:**
- Melihat daftar supplier
- Menambah supplier baru
- Mengubah data supplier
- Menghapus supplier

---

### 4.3 Transaksi

Bagian ini berisi pencatatan pergerakan barang masuk dan keluar.

#### 4.3.1 Barang Masuk

**Icon:** Panah ke Bawah (↓)

Menu untuk mencatat penerimaan barang dari supplier. Setiap pencatatan barang masuk akan **menambah stok** produk secara otomatis.

**Data yang dicatat:**

| Field | Keterangan |
|-------|------------|
| No. Invoice | Nomor invoice dari supplier (diisi manual) |
| Supplier | Nama supplier pengirim barang |
| Tanggal Transaksi | Tanggal penerimaan barang |
| Catatan | Keterangan tambahan (opsional) |
| Daftar Barang | List barang yang diterima (bisa lebih dari 1 item) |

**Untuk setiap item di daftar barang:**

| Field | Keterangan |
|-------|------------|
| Nama Barang | Produk yang diterima |
| Harga Satuan | Harga per unit (otomatis mengisi dari harga beli produk) |
| Jumlah Masuk | Banyaknya barang yang diterima |
| Subtotal | Harga Satuan x Jumlah (dihitung otomatis) |

**Yang bisa dilakukan:**
- Melihat daftar semua transaksi barang masuk
- Menambah catatan barang masuk baru
- Melihat detail transaksi
- Mengubah data transaksi
- Menghapus transaksi

#### 4.3.2 Barang Keluar

**Icon:** Panah ke Atas (↑)

Menu untuk mencatat pengeluaran barang (penjualan/pengiriman). Setiap pencatatan barang keluar akan **mengurangi stok** produk secara otomatis.

**Data yang dicatat:**

| Field | Keterangan |
|-------|------------|
| No. Invoice | Nomor invoice (dibuat otomatis oleh sistem, format: `INV/OUT/Tanggal/Urutan`) |
| Tanggal Transaksi | Tanggal pengeluaran barang |
| Catatan | Keterangan tambahan (opsional) |
| Daftar Barang | List barang yang keluar (bisa lebih dari 1 item) |

**Untuk setiap item di daftar barang:**

| Field | Keterangan |
|-------|------------|
| Nama Barang | Produk yang dikirim |
| Harga Satuan | Harga per unit (otomatis mengisi dari harga jual produk) |
| Jumlah Keluar | Banyaknya barang yang dikirim |
| Subtotal | Harga Satuan x Jumlah (dihitung otomatis) |

**Yang bisa dilakukan:**
- Melihat daftar semua transaksi barang keluar
- Menambah catatan barang keluar baru
- Melihat detail transaksi
- Mengubah data transaksi
- Mencetak dokumen barang keluar
- Menghapus transaksi

> **Perhatian:** Jumlah barang keluar tidak boleh melebihi stok yang tersedia. Jika stok tidak cukup, tombol "Simpan" akan dinonaktifkan.

---

### 4.4 Laporan

Bagian ini berisi laporan dan riwayat pergerakan stok.

#### 4.4.1 Mutasi Stok

**Icon:** Panah Melingkar

Menampilkan **seluruh riwayat pergerakan stok** produk, baik masuk maupun keluar, secara kronologis.

**Data yang ditampilkan:**

| Kolom | Keterangan |
|-------|------------|
| Nama Produk | Nama barang yang bergerak |
| Tipe Transaksi | "Masuk" (hijau) atau "Keluar" (merah) |
| Jumlah | Banyaknya barang yang bergerak |
| Harga Satuan | Harga per unit saat transaksi |
| Subtotal | Total harga untuk baris tersebut |
| Tanggal Transaksi | Kapan transaksi terjadi |
| Catatan | Keterangan tambahan |

> **Catatan Penting:** Data mutasi stok **tidak bisa ditambah/diubah/dihapus** secara manual. Data ini dibuat **secara otomatis** setiap kali ada transaksi barang masuk atau barang keluar. Ini memastikan data pergerakan stok selalu akurat dan lengkap.

#### 4.4.2 Laporan Stok

**Icon:** Dokumen

Halaman laporan lengkap yang menampilkan **kondisi stok semua produk** beserta jumlah total masuk dan keluar.

**Data yang ditampilkan:**

| Kolom | Keterangan |
|-------|------------|
| SKU | Kode produk |
| Nama Produk | Nama barang |
| Kategori | Kelompok barang |
| Satuan | Unit pengukuran |
| Stok Masuk | Total jumlah barang yang pernah masuk |
| Stok Keluar | Total jumlah barang yang pernah keluar |
| Sisa Stok | Stok saat ini |

**Fitur khusus:**
- **Filter berdasarkan kategori** — dropdown untuk memfilter hanya produk dalam kategori tertentu
- **Export XLSX** — mengunduh laporan dalam format Excel
- **Export PDF** — membuka halaman cetak laporan dalam format PDF di tab baru

---

### 4.5 Manajemen Pengguna

**Icon:** Pengguna (di bagian paling bawah sidebar)

Menu untuk mengelola akun pengguna yang bisa mengakses sistem.

**Data yang ditampilkan:**

| Kolom | Keterangan |
|-------|------------|
| Nama | Nama lengkap pengguna |
| Email | Alamat email untuk login |
| Role | Peran/jabatan pengguna |
| Dibuat pada | Waktu akun dibuat |
| Diperbarui pada | Waktu terakhir akun diubah |

**Yang bisa dilakukan:**
- Melihat daftar semua pengguna
- Menambah pengguna baru (dengan nama, email, password, dan role)
- Mengubah data pengguna
- Menghapus pengguna (dalam bentuk bulk/bersamaan)

---

### 4.6 Pengaturan Akun

**Icon:** Roda Gigi (di menu profil, pojok kanan atas)

Halaman untuk mengubah data akun yang sedang login.

**Yang bisa diubah:**
- Nama
- Email
- Password baru

---

## 5. Role & Hak Akses

Sistem ini memiliki **3 role (peran)** yang menentukan apa yang bisa dilihat dan dilakukan oleh setiap pengguna.

### Ringkasan Hak Akses

| Fitur | Super Admin | Admin | Kasir |
|-------|:-----------:|:-----:|:-----:|
| **Dashboard** | ✅ | ✅ | ✅ |
| **Produk** | | | |
| &nbsp;&nbsp;Melihat daftar | ✅ | ✅ | ✅ |
| &nbsp;&nbsp;Menambah produk | ✅ | ✅ | ❌ |
| &nbsp;&nbsp;Mengubah produk | ✅ | ✅ | ❌ |
| &nbsp;&nbsp;Menghapus produk | ✅ | ✅ | ❌ |
| **Kategori** | | | |
| &nbsp;&nbsp;Melihat daftar | ✅ | ✅ | ✅ |
| &nbsp;&nbsp;Menambah/mengubah/hapus | ✅ | ✅ | ❌ |
| **Supplier** | | | |
| &nbsp;&nbsp;Melihat daftar | ✅ | ✅ | ✅ |
| &nbsp;&nbsp;Menambah/mengubah/hapus | ✅ | ✅ | ❌ |
| **Barang Masuk** | | | |
| &nbsp;&nbsp;Melihat daftar | ✅ | ✅ | ✅ |
| &nbsp;&nbsp;Menambah transaksi | ✅ | ✅ | ✅ |
| &nbsp;&nbsp;Mengubah/menghapus transaksi | ✅ | ✅ | ❌ |
| **Barang Keluar** | | | |
| &nbsp;&nbsp;Melihat daftar | ✅ | ✅ | ✅ |
| &nbsp;&nbsp;Menambah transaksi | ✅ | ✅ | ✅ |
| &nbsp;&nbsp;Mengubah/menghapus transaksi | ✅ | ✅ | ❌ |
| **Mutasi Stok** | | | |
| &nbsp;&nbsp;Melihat laporan | ✅ | ✅ | ✅ |
| &nbsp;&nbsp;Menambah/mengubah/menghapus | ❌* | ❌* | ❌* |
| **Laporan Stok** | ✅ | ✅ | ✅ |
| **Export Excel/PDF** | ✅ | ✅ | ✅ |
| **Manajemen Pengguna** | ✅ | ✅ | ❌ |
| **Pengaturan Role & Permission** | ✅ | ✅ | ❌ |

> *Mutasi stok dibuat otomatis oleh sistem, tidak bisa diedit oleh siapapun.

### Penjelasan Role

#### 🔴 Super Admin
- **Siapa:** Pemilik usaha atau admin utama
- **Hak Akses:** Penuh — bisa melakukan semua hal di sistem
- **Keterangan:** Memiliki akses ke semua fitur termasuk manajemen pengguna dan pengaturan role

#### 🟠 Admin
- **Siapa:** Admin gudang atau manajer
- **Hak Akses:** Sama dengan Super Admin
- **Keterangan:** Bisa mengelola semua data dan transaksi, termasuk mengelola pengguna lain

#### 🟢 Kasir
- **Siapa:** Staff kasir atau pegawai gudang
- **Hak Akses:** Terbatas — hanya bisa melihat data dan mencatat transaksi masuk/keluar
- **Keterangan:**
  - Bisa **melihat** data produk, kategori, supplier, dan laporan
  - Bisa **menambah** transaksi barang masuk dan barang keluar
  - **Tidak bisa** mengubah atau menghapus data produk, kategori, atau supplier
  - **Tidak bisa** mengelola pengguna lain

---

## 6. Alur Kerja Utama

### 6.1 Menambah Produk Baru

```
1. Klik menu "Produk" di sidebar
2. Klik tombol "+ Produk Baru" di pojok kanan atas
3. Isi formulir:
   - Nama Produk (wajib)
   - Kategori (pilih dari dropdown)
   - SKU (kode unik produk, wajib)
   - Harga Beli (Rp)
   - Harga Jual (Rp) — harus lebih besar atau sama dengan Harga Beli
   - Stok Awal (jumlah awal barang)
   - Satuan (pilih dari dropdown, bisa tambah baru langsung)
4. Klik "Simpan"
```

### 6.2 Mencatat Barang Masuk

```
1. Klik menu "Barang Masuk" di sidebar
2. Klik tombol "+ Barang Masuk" di pojok kanan atas
3. Isi data header:
   - No. Invoice (dari supplier)
   - Supplier (pilih dari dropdown)
   - Tanggal Transaksi
   - Catatan (opsional)
4. Tambah item barang:
   - Klik "+ Tambah Barang"
   - Pilih Nama Barang (harga satuan otomatis terisi dari harga beli)
   - Isi Jumlah Masuk
   - Subtotal dihitung otomatis
5. Bisa menambah beberapa item sekaligus
6. Klik "Simpan"
7. Stok produk akan bertambah otomatis
```

### 6.3 Mencatat Barang Keluar

```
1. Klik menu "Barang Keluar" di sidebar
2. Klik tombol "+ Barang Keluar" di pojok kanan atas
3. Isi data header:
   - No. Invoice (dibuat otomatis oleh sistem)
   - Tanggal Transaksi
   - Catatan (opsional)
4. Tambah item barang:
   - Klik "+ Tambah Barang"
   - Pilih Nama Barang (harga satuan otomatis terisi dari harga jual)
   - Isi Jumlah Keluar (tidak boleh melebihi stok yang tersedia)
   - Subtotal dihitung otomatis
5. Bisa menambah beberapa item sekaligus
6. Klik "Simpan"
7. Stok produk akan berkurang otomatis
```

### 6.4 Melihat Laporan Stok

```
1. Klik menu "Laporan Stok" di sidebar
2. Tabel akan menampilkan semua produk beserta stok masuk, keluar, dan sisa
3. (Opsional) Filter berdasarkan kategori dengan dropdown di atas tabel
4. (Opsional) Export:
   - Klik "Export XLSX" untuk mengunduh file Excel
   - Klik "Export PDF" untuk membuka halaman cetak PDF di tab baru
```

### 6.5 Mencetak Dokumen Barang Keluar

```
1. Klik menu "Barang Keluar" di sidebar
2. Klik ikon 👁 (lihat) pada transaksi yang ingin dicetak
3. Klik tombol "Cetak" di pojok kanan atas
4. Dokumen akan terbuka di tab baru, siap untuk dicetak melalui printer
```

---

## 7. Fitur Dashboard

Dashboard adalah halaman pertama yang terlihat setelah login. Dashboard menampilkan informasi dalam 3 bagian:

### 7.1 Kartu Statistik — Ringkasan Inventori

| Kartu | Keterangan | Warna |
|-------|------------|-------|
| **Total Produk** | Jumlah semua produk yang terdaftar | Abu-abu |
| **Total Unit Stok** | Total jumlah semua barang di gudang | Biru |
| **Nilai Inventori (HPP)** | Total nilai stok berdasarkan harga beli | Hijau |
| **Nilai Inventori (Jual)** | Total nilai stok berdasarkan harga jual | Biru |
| **Stok Habis** | Jumlah produk yang stoknya 0 | Merah (jika > 0) / Hijau (jika 0) |
| **Stok Menipis** | Jumlah produk yang stoknya ≤ 10 | Kuning (jika > 0) / Hijau (jika 0) |

### 7.2 Kartu Statistik — Aktivitas Transaksi

| Kartu | Keterangan |
|-------|------------|
| **Barang Masuk Hari Ini** | Total jumlah barang yang masuk hari ini |
| **Barang Keluar Hari Ini** | Total jumlah barang yang keluar hari ini |
| **Transaksi Masuk Bulan Ini** | Jumlah transaksi barang masuk sebulan terakhir |
| **Transaksi Keluar Bulan Ini** | Jumlah transaksi barang keluar sebulan terakhir |
| **Kategori** | Jumlah kategori yang terdaftar |
| **Supplier** | Jumlah supplier yang terdaftar |

### 7.3 Grafik

| Grafik | Tipe | Keterangan |
|--------|------|------------|
| **Pergerakan Stok (30 Hari)** | Garis | Menampilkan jumlah barang masuk dan keluar per hari selama 30 hari terakhir |
| **Stok per Kategori** | Donat/Lingkaran | Menyebar stok berdasarkan kategori (maksimal 8 kategori teratas) |
| **Produk Terlaris Keluar** | Batang Horisontal | 10 produk dengan jumlah keluar terbanyak dalam 30 hari |

### 7.4 Tabel

| Tabel | Keterangan |
|-------|------------|
| **Produk Stok Rendah / Habis** | Daftar produk dengan stok ≤ 10, diurutkan dari yang paling sedikit |
| **Mutasi Stok Terbaru** | 10 transaksi mutasi stok terakhir (masuk dan keluar) |

---

## 8. Fitur Export & Cetak

### 8.1 Export ke Excel (.xlsx)

**Di mana:** Menu Laporan Stok > tombol "Export XLSX"

**Yang diunduh:** File Excel berisi data lengkap semua produk beserta stok masuk, keluar, dan sisa stok.

**Nama file:** `laporan_stok_Tanggal_Waktu.xlsx` (contoh: `laporan_stok_20260712_143022.xlsx`)

### 8.2 Export ke PDF

**Di mana:** Menu Laporan Stok > tombol "Export PDF"

**Yang didapat:** Halaman cetak dalam format PDF yang terbuka di tab baru browser. Bisa langsung dicetak atau disimpan sebagai file PDF.

### 8.3 Cetak Dokumen Barang Keluar

**Di mana:** Menu Barang Keluar > Klik ikon 👁 > tombol "Cetak"

**Yang didapat:** Dokumen cetak barang keluar yang berisi detail transaksi (invoice, daftar barang, jumlah, harga, total).

### 8.4 Cetak Laporan Stok (Browser Print)

**Di mana:** Menu Laporan Stok > tombol "Export PDF"

**Yang didapat:** Tampilan laporan stok yang bisa dicetak langsung dari browser (Ctrl+P / Cmd+P).

---

## 9. Aturan Bisnis (Business Rules)

Sistem ini memiliki beberapa aturan yang diterapkan untuk menjaga keakuratan data:

### 9.1 Harga Jual ≥ Harga Beli

Saat menambah atau mengubah produk, **harga jual harus lebih besar atau sama dengan harga beli**. Jika tidak, tombol "Simpan" akan dinonaktifkan dan sistem akan menampilkan peringatan.

> **Alasan:** Mencegah kerugian — tidak menjual barang di bawah harga beli.

### 9.2 Stok Tidak Boleh Minus

Saat mencatat barang keluar, **jumlah yang dikeluarkan tidak boleh melebihi stok yang tersedia**. Jika stok tidak cukup:
- Tombol "Simpan" dinonaktifkan
- Peringatan merah ditampilkan di layar

> **Alasan:** Memastikan数据 stok selalu valid dan tidak ada pengiriman barang yang tidak realistis.

### 9.3 Invoice Barang Keluar Otomatis

Setiap transaksi barang keluar mendapatkan nomor invoice **secara otomatis** dengan format:
```
INV/OUT/TahunBulanTanggal/Urutan
```
Contoh: `INV/OUT/20260712/0001`

> **Alasan:** Memudahkan pencatatan dan pelacakan tanpa perlu mengisi nomor invoice manual.

### 9.4 Harga Satuan Otomatis Terisi

Saat memilih produk di form barang masuk atau keluar, **harga satuan akan otomatis terisi**:
- **Barang Masuk:** Harga satuan = Harga Beli produk
- **Barang Keluar:** Harga satuan = Harga Jual produk

Harga ini masih bisa diubah manual jika diperlukan.

### 9.5 Subtotal dan Total Harga Dihitung Otomatis

- **Subtotal** = Harga Satuan × Jumlah (dihitung real-time saat mengisi form)
- **Total Harga** (pada header transaksi) = Total dari semua subtotal item

### 9.6 Mutasi Stok Otomatis

Setiap kali ada transaksi barang masuk atau keluar, **satu catatan mutasi stok akan dibuat secara otomatis**. Data ini tidak bisa diubah atau dihapus oleh pengguna, sehingga menjadi **jejak audit** yang andal.

### 9.7 Stok Produk Diperbarui Otomatis

- **Barang Masuk:** Stok produk **bertambah** sejumlah jumlah yang diterima
- **Barang Keluar:** Stok produk **berkurang** sejumlah jumlah yang dikeluarkan
- **Edit/Hapus Transaksi:** Stok akan disesuaikan kembali secara otomatis

### 9.8 Produk Tidak Boleh Dipilih Dua Kali dalam Satu Transaksi

Saat mengisi daftar barang dalam satu transaksi, **produk yang sama tidak bisa dipilih lebih dari satu kali**. Opsi produk yang sudah dipilih akan dinonaktifkan di dropdown berikutnya.

> **Alasan:** Mencegah duplikasi data — jumlah harus dijumlahkan dalam satu baris, bukan dipisah.

---

## 10. Struktur Data (Simplified)

Bagian ini menjelaskan bagaimana data-data dalam sistem saling berkaitan.

### Diagram Relasi

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│  KATEGORI   │     │   PRODUK    │     │    UNIT     │
│─────────────│     │─────────────│     │─────────────│
│ • Nama      │◄────│ • Nama      │────►│ • Nama      │
│ • Deskripsi │  1  │ • SKU       │  1  │  (Pcs, Kg,  │
│             │  :  │ • Harga Beli│  :  │   Liter, dll)│
│             │  M  │ • Harga Jual│  M  │             │
│             │     │ • Stok      │     │             │
└─────────────┘     └──────┬──────┘     └─────────────┘
                           │
              ┌────────────┼────────────┐
              │            │            │
              ▼            ▼            ▼
     ┌──────────────┐ ┌──────────┐ ┌──────────────────┐
     │ BARANG MASUK │ │BARANG    │ │  MUTASI STOK     │
     │──────────────│ │KELUAR    │ │──────────────────│
     │ • No. Invoice│ │──────────│ │ • Tipe (Masuk/   │
     │ • Supplier   │ │• No.Inv  │ │   Keluar)        │
     │ • Tanggal    │ │• Tanggal │ │ • Jumlah          │
     │ • Total Harga│ │• Total   │ │ • Tanggal         │
     │              │ │  Harga   │ │ • Harga Satuan    │
     │ Item Detail: │ │          │ │ • Catatan         │
     │ • Produk     │ │Item Det: │ │                   │
     │ • Harga      │ │• Produk  │ │ (Dibuat otomatis  │
     │ • Jumlah     │ │• Harga   │ │  oleh sistem)     │
     │ • Subtotal   │ │• Jumlah  │ │                   │
     └──────┬───────┘ │• Subtotal│ └──────────────────┘
            │         └────┬─────┘
            │              │
            └──────┬───────┘
                   │
            ┌──────▼───────┐
            │   SUPPLIER   │
            │──────────────│
            │ • Nama       │
            │ • Telepon    │
            │ • Alamat     │
            └──────────────┘
```

### Penjelasan Sederhana

| Hubungan | Penjelasan |
|----------|------------|
| **Kategori → Produk** | Satu kategori bisa berisi banyak produk. Satu produk termasuk dalam satu kategori. |
| **Unit → Produk** | Satu satuan (misal: Kg) bisa dipakai untuk banyak produk. Satu produk menggunakan satu satuan. |
| **Supplier → Barang Masuk** | Satu supplier bisa mengirim banyak barang masuk. Satu barang masuk berasal dari satu supplier. |
| **Produk → Barang Masuk/Keluar** | Satu produk bisa masuk/keluar berkali-kali. Satu transaksi bisa memuat banyak produk. |
| **Barang Masuk/Keluar → Mutasi Stok** | Setiap item di barang masuk atau keluar akan membuat satu catatan mutasi stok secara otomatis. |

---

## 11. FAQ (Pertanyaan Umum)

### Q: Lupa password, bagaimana cara reset?

Hubungi administrator sistem untuk mengatur ulang password Anda.

### Q: Kenapa tombol "Simpan" dinonaktifkan saat mengisi form?

Kemungkinan ada data yang belum valid:
- **Harga Jual < Harga Beli** — harga jual harus lebih besar atau sama dengan harga beli
- **Stok tidak cukup** (di form barang keluar) — jumlah yang dikeluarkan melebihi stok tersedia
- **Ada field wajib yang belum diisi** — periksa semua field bertanda (*)

### Q: Apakah data yang sudah dihapus bisa dikembalikan?

Tidak. Penghapusan data bersifat permanen. Pastikan data yang dihapus memang sudah tidak diperlukan.

### Q: Bagaimana jika produk sudah ada di transaksi sebelumnya?

Jika produk sudah memiliki riwayat transaksi, menghapus data produk juga akan menghapus data transaksi terkait (karena hubungan antar data). Hati-hati dalam menghapus data.

### Q: Apakah ada batasan jumlah produk yang bisa didaftarkan?

Tidak ada batasan. Anda bisa mendaftarkan sebanyak mungkin produk yang diperlukan.

### Q: Bagaimana cara menambah satuan baru (misal: Pack, Karton)?

Saat mengisi form produk, pada field **Satuan** klik dropdown, lalu pilih opsi **"+ Create"** untuk menambah satuan baru secara langsung tanpa perlu ke menu terpisah.

### Q: Apakah harga satuan di transaksi harus sesuai dengan harga di data produk?

Tidak harus. Harga satuan di transaksi **otomatis terisi** berdasarkan data produk, tapi masih **bisa diubah manual** jika ada perbedaan harga (misal: harga grosir berbeda dari harga eceran).

### Q: Bagaimana cara melihat siapa yang melakukan perubahan data?

Untuk saat ini, sistem belum mencatat log perubahan data secara detail. Namun, semua data transaksi (barang masuk, barang keluar, mutasi stok) tercatat dengan tanggal dan tidak bisa diubah seenaknya.

### Q: Bisa tidak mengubah nomor invoice barang keluar?

Tidak. Nomor invoice barang keluar dibuat **secara otomatis** oleh sistem dan tidak bisa diubah. Ini untuk mencegah duplikasi dan menjaga konsistensi nomor urut.

### Q: Bagaimana cara mencetak laporan stok?

1. Buka menu **Laporan Stok**
2. Klik tombol **Export PDF** — halaman cetak akan terbuka di tab baru
3. Tekan **Ctrl+P** (Windows) atau **Cmd+P** (Mac) untuk mencetak
4. Pilih printer dan klik **Cetak**

Alternatif: Klik **Export XLSX** untuk mengunduh file Excel yang bisa dicetak dari aplikasi spreadsheet.

---

*Dokumen ini disusun untuk kebutuhan internal tim pengelola inventori. Untuk pertanyaan lebih lanjut, silakan hubungi administrator sistem.*

# 💀 PROJECT MANAGEMENT: GRANDONG PERFORMANCE HUB
*Status Proyek: Pengembangan Backend & Integrasi Database (Tahap CRUD)*
*Terakhir Diperbarui: Juni 2026*

---

## 1. DESKRIPSI SINGKAT PROYEK
Grandong adalah platform katalog e-commerce aftermarket otomotif dengan tema visual gelap maskulin (*dark mode* hitam-merah). Proyek ini dibangun menggunakan arsitektur *Decoupled/API-driven* sesuai standar perkuliahan pemrograman web. Data produk ditarik secara dinamis dari database MySQL menggunakan PHP PDO sebagai penyedia API data (JSON), kemudian dirender di frontend menggunakan JavaScript Fetch API tanpa muat ulang halaman (*no reload*).

---

## 2. STRUKTUR FILE SAAT INI
*   📁 `index.html` — Halaman landing utama (Filosofi & navigasi).
*   📁 `sparepart.html` — Katalog dinamis yang dilengkapi dropdown filter kategori.
*   📁 `detail.html` — Halaman detail spesifikasi teknis komponen secara mendalam.
*   📁 `cart.html` — Halaman invoice keranjang belanja (Hitung PPN 11% & checkout WhatsApp).
*   📁 `admin.php` *(NEW/UPDATED)* — Control Panel Admin untuk manajemen inventaris langsung ke MySQL.
*   📁 `koneksi.php` — Modul koneksi basis data menggunakan PHP PDO (Port: 3307).
*   📁 `get_parts.php` — API lokal penyuplai data JSON dari tabel database ke JavaScript.
*   📁 `style.css` — Pusat manajemen gaya, toast kustom, dan select box bertema *custom culture*.
*   📁 `script.js` — Logika Fetch API database, sistem *local storage* keranjang, enkripsi teks WA, dan UI.

---

## 3. PROGRESS LOG (WHAT HAS BEEN DONE) ✅

### 🖥️ Sektor Database & Backend API
*   [x] **Konfigurasi Server Lokal:** Berhasil mengaktifkan database `dbgrandong` menggunakan koneksi PDO aman lewat port **3307** (`koneksi.php`).
*   [x] **Penyusunan Skema Basis Data:** Tabel `parts` sudah siap dengan struktur kolom: `id`, `nama_part`, `brand`, `harga`, `gambar`, `stok`, `kategori`, `tipe`, `deskripsi`, dan `spesifikasi` (Format data JSON kaku).
*   [x] **Pembuatan API Handler:** File `get_parts.php` sukses bertindak sebagai jembatan data, melakukan *decode* format JSON internal MySQL, dan menyajikannya dalam respon `application/json`.

### 🎛️ Sektor Panel Admin (`admin.php`)
*   [x] **Operasi Read All:** Menampilkan seluruh data sparepart langsung dari tabel MySQL ke dalam tabel inventaris web lengkap dengan penghitung total item.
*   [x] **Operasi Create Data:** Menyediakan form input terpadu untuk menyuntikkan (*inject*) komponen balap baru ke dalam database.
*   [x] **Operasi Update Data:** Menyediakan fitur tombol edit yang otomatis menarik data spesifik ke form untuk diperbarui.
*   [x] **Operasi Delete Data:** Menyediakan tombol hapus dengan konfirmasi *pop-up* bawaan untuk membuang komponen langsung dari baris database.

### 🛒 Sektor Frontend & Sistem Keranjang (`script.js` & `cart.html`)
*   [x] **Sinkronisasi Kode:** Menuntaskan masalah ketidakcocokan tipe data (*strict comparison* `===`) pada pencarian item keranjang.
*   [x] **Integrasi LocalStorage:** Memastikan penyimpanan keranjang terkunci pada satu nama kunci tunggal (`grandong_cart`) untuk menghindari hilangnya data antar halaman.
*   [x] **Penyesuaian Properti Objek:** Menambahkan fungsi normalisasi otomatis agar JavaScript bisa membaca properti data nama produk baik dari array hardcode lama (`nama`) maupun kolom database baru (`nama_part`).
*   [x] **Matematika Keranjang:** Perhitungan Subtotal, Pajak PPN 11%, dan Total Akhir belanjaan berjalan akurat serta sinkron dengan generator teks checkout WhatsApp.

---

## 4. NEXT TARGETS (WHAT TO DO NEXT) 🚀

### 🛠️ Prioritas 1: Migrasi Data Katalog Total ke MySQL
*   **Masalah Saat Ini:** File `script.js` masih menyimpan cadangan variabel array lokal `var spareparts = [...]` sebanyak 35 item di baris paling atas.
*   **Tindakan:** 
    1. Hapus isi array *hardcode* lokal di dalam `script.js`.
    2. Aktifkan pemanggilan fungsi `fetchPartsFromDB()` tepat di dalam blok `window.onload` di halaman katalog (`sparepart.html`) agar data murni mengalir dari file `get_parts.php`.
    3. Pastikan sistem pencarian ketik (*search bar*) dan fungsi dropdown filter kategori tidak pecah/error setelah beralih ke data database penuh.

### 💾 Prioritas 2: Fitur Unggah File Gambar Lokal di Admin
*   **Masalah Saat Ini:** Input gambar pada form `admin.php` masih menggunakan ketik manual berupa alamat link URL internet atau path teks (`img/namafile.jpg`).
*   **Tindakan:**
    1. Ubah tipe input gambar di form `admin.php` menjadi `<input type="file" name="gambar_file">`.
    2. Tambahkan atribut `enctype="multipart/form-data"` pada tag form di `admin.php`.
    3. Buat skrip validasi backend PHP di bagian atas `admin.php` untuk memproses pemindahan file fisik gambar (`move_uploaded_file`) ke dalam direktori lokal proyek (folder `img/`) dan simpan nama filenya ke database.

### 📋 Prioritas 3: Form Identitas Konsumen saat Checkout
*   **Rencana:** Menambahkan input teks nama pembeli dan alamat/catatan mekanik di halaman `cart.html` sebelum melempar data ke API WhatsApp, agar data pesanan yang diterima oleh Tim Grandong di WA menjadi lebih rapi dan jelas.
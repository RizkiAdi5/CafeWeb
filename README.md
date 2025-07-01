# KOPTE TARIK - Sistem Pemesanan Online

## Deskripsi

Sistem pemesanan online untuk KOPTE TARIK yang memungkinkan pelanggan untuk memesan menu kopi dan makanan secara online. Sistem ini dilengkapi dengan panel admin untuk mengelola produk, pesanan, user, dan pesan kontak.

## Fitur Utama

### 1. Sistem Autentikasi

- Login dan registrasi user
- Login admin dengan sistem terpisah
- Session management
- Proteksi halaman checkout dan admin

### 2. Katalog Menu

- Tampilan menu dengan gambar dan harga
- Tombol "Tambahkan ke Keranjang" untuk setiap menu
- Halaman menu lengkap dengan fitur pencarian
- Responsive design

### 3. Keranjang Belanja (Cart)

- Menambahkan item ke keranjang
- Mengubah jumlah item
- Menghapus item dari keranjang
- Perhitungan total otomatis

### 4. Checkout System

- Form pengisian data pengiriman
- Pilihan metode pembayaran
- Validasi input
- Proses checkout dengan AJAX

### 5. Riwayat Pesanan

- Melihat semua pesanan user
- Detail pesanan lengkap
- Status pesanan

### 6. Panel Admin

- Dashboard dengan statistik
- Manajemen produk (CRUD)
- Manajemen user (CRUD)
- Manajemen pesanan (CRUD)
- Manajemen admin (CRUD)
- Monitoring pesan kontak

### 7. Sistem Kontak

- Form kontak untuk pelanggan
- Penyimpanan pesan ke database
- Monitoring pesan di panel admin

## Struktur Database

### Tabel `users`

- `id` - Primary key
- `username` - Username unik
- `email` - Email unik
- `password` - Password (plain text - untuk demo)
- `created_at` - Timestamp pembuatan akun

### Tabel `admins`

- `id` - Primary key
- `username` - Username unik
- `email` - Email unik
- `password` - Password (hashed)
- `created_at` - Timestamp pembuatan akun

### Tabel `products`

- `id` - Primary key
- `name` - Nama produk
- `price` - Harga produk
- `image` - Nama file gambar
- `description` - Deskripsi produk
- `stock` - Stok produk
- `created_at` - Timestamp pembuatan

### Tabel `orders`

- `id` - Primary key
- `user_id` - Foreign key ke users
- `total_amount` - Total pembayaran
- `status` - Status pesanan (pending/confirmed/completed/cancelled)
- `order_date` - Tanggal pesanan
- `delivery_address` - Alamat pengiriman
- `phone` - Nomor telepon
- `payment_method` - Metode pembayaran

### Tabel `order_items`

- `id` - Primary key
- `order_id` - Foreign key ke orders
- `product_id` - Foreign key ke products
- `quantity` - Jumlah item
- `price` - Harga per item

### Tabel `contacts`

- `id` - Primary key
- `name` - Nama pengirim
- `email` - Email pengirim
- `subject` - Subjek pesan
- `message` - Isi pesan
- `created_at` - Timestamp pengiriman

## File Utama

### Halaman User

#### 1. `index.php`

- Halaman utama website
- Menampilkan menu dengan limit 6 item
- Tombol "Lihat Menu Lengkap"
- Header dengan navigasi dan cart icon
- Form kontak

#### 2. `fullmenu.php`

- Halaman menu lengkap
- Fitur pencarian menu
- Tampilan stok produk
- Tombol "Tambahkan ke Keranjang"

#### 3. `cart.php`

- Halaman keranjang belanja
- Menampilkan item yang dipilih
- Fitur update quantity dan hapus item
- Tombol checkout

#### 4. `checkout.php`

- Halaman checkout
- Form pengisian data pengiriman
- Ringkasan pesanan
- Proses checkout dengan AJAX

#### 5. `order_success.php`

- Halaman sukses setelah checkout
- Menampilkan detail pesanan
- Order ID dan status

#### 6. `orders.php`

- Halaman riwayat pesanan
- Menampilkan semua pesanan user
- Detail lengkap setiap pesanan

#### 7. `login.php` & `signup.php`

- Halaman autentikasi user
- Validasi input
- Session management

### Halaman Admin

#### 1. `admin/index.php`

- Dashboard admin
- Statistik user, produk, pesanan, admin
- Quick overview sistem

#### 2. `admin/products.php`

- Manajemen produk (CRUD)
- Tambah, edit, hapus produk
- Upload gambar produk

#### 3. `admin/users.php`

- Manajemen user (CRUD)
- Tambah, edit, hapus user
- Reset password

#### 4. `admin/orders.php`

- Manajemen pesanan (CRUD)
- Update status pesanan
- Detail pesanan lengkap

#### 5. `admin/admins.php`

- Manajemen admin (CRUD)
- Tambah, edit, hapus admin
- Sistem login admin

#### 6. `admin/contacts.php`

- Monitoring pesan kontak
- Detail pesan dengan AJAX
- Status pesan

### File Pendukung

#### 1. `db.php`

- Konfigurasi database
- Pembuatan tabel otomatis
- Insert data produk default

#### 2. `process_checkout.php`

- API endpoint untuk memproses checkout
- Menggunakan AJAX
- Validasi dan penyimpanan data

#### 3. `process_contact.php`

- API endpoint untuk memproses form kontak
- Validasi dan penyimpanan pesan

#### 4. `css/style.css`

- Styling utama website
- Responsive design
- Animasi dan efek visual

## Cara Penggunaan

### 1. Setup Database

1. Pastikan XAMPP/WAMP sudah terinstall
2. Start Apache dan MySQL
3. Buka website di browser
4. Database akan dibuat otomatis saat pertama kali diakses

### 2. Registrasi User

1. Klik icon user di header
2. Pilih "Signup"
3. Isi form registrasi
4. Login dengan akun yang dibuat

### 3. Memesan Menu

1. Pilih menu yang diinginkan
2. Klik "Tambahkan ke Keranjang"
3. Klik icon keranjang di header untuk melihat cart
4. Atur jumlah item jika diperlukan
5. Klik "Checkout"

### 4. Checkout

1. Isi form data pengiriman
2. Pilih metode pembayaran
3. Klik "Konfirmasi Pesanan"
4. Pesanan akan diproses dan redirect ke halaman sukses

### 5. Melihat Riwayat Pesanan

1. Login ke akun
2. Klik icon user di header
3. Pilih "Riwayat Pesanan"
4. Lihat semua pesanan yang telah dibuat

### 6. Panel Admin

1. Akses `/admin/login.php`
2. Login dengan kredensial admin
3. Kelola produk, user, pesanan, dan admin
4. Monitor pesan kontak

## Teknologi yang Digunakan

- **Frontend**: HTML5, CSS3, JavaScript, Font Awesome
- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Server**: Apache (XAMPP/WAMP)
- **Framework**: AOS (Animate On Scroll)
- **Slider**: Swiper.js
- **AJAX**: Untuk interaksi dinamis
- **Session**: PHP Session untuk state management

## Keamanan

### Yang Sudah Diimplementasi

- Session management untuk user dan admin login
- Validasi input di sisi server
- Prepared statements untuk mencegah SQL injection
- Redirect protection untuk halaman checkout dan admin
- Password hashing untuk admin
- Popup konfirmasi untuk semua aksi

### Rekomendasi Peningkatan

- Enkripsi password dengan bcrypt untuk user
- CSRF protection
- Rate limiting untuk API
- Input sanitization yang lebih ketat
- HTTPS untuk production
- Logging untuk audit trail

## Struktur Folder

```
UAS-WebLanjut/
├── admin/
│   ├── index.php
│   ├── login.php
│   ├── logout.php
│   ├── sidebar.php
│   ├── products.php
│   ├── users.php
│   ├── orders.php
│   ├── admins.php
│   ├── contacts.php
│   └── get_contact_details.php
├── css/
│   └── style.css
├── image/
│   ├── menu1.png
│   ├── menu2.png
│   └── ... (semua gambar menu)
├── db.php
├── index.php
├── fullmenu.php
├── login.php
├── signup.php
├── logout.php
├── cart.php
├── checkout.php
├── order_success.php
├── orders.php
├── process_checkout.php
├── process_contact.php
├── add_to_cart.php
├── update_cart.php
├── remove_item.php
├── clear_cart.php
└── README.md
```

## Catatan Penting

1. **Password**: Password admin menggunakan hash, password user masih plain text untuk demo. Untuk production, gunakan bcrypt atau hash yang aman.

2. **Database**: Pastikan MySQL berjalan dan kredensial di `db.php` sesuai dengan konfigurasi server Anda.

3. **Gambar**: Semua gambar menu harus ada di folder `image/` dengan nama file yang sesuai dengan database.

4. **Session**: Sistem menggunakan PHP session untuk menyimpan cart dan user/admin login.

5. **Popup**: Semua aksi menggunakan popup konfirmasi untuk UX yang lebih baik.

6. **Stock**: Sistem menampilkan stok produk di halaman menu lengkap.

## Kontributor

- Ardiansyah
- Abang Malik Syahidar
- Mohammad Dimas Al Fateh

## Lisensi

© 2025 KOPTE TARIK. All rights reserved.

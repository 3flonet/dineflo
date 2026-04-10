# 🏛️ Dineflo v2 - Master Project Overview & 44 Core Features Guide

## 📋 Pendahuluan
**Dineflo v2** adalah platform SaaS Manajemen Restoran & POS yang menggunakan arsitektur **Multi-Tenancy** native. Dokumen ini merincikan 44 fitur utama yang dirancang untuk mendigitalisasi seluruh aspek operasional restoran, mulai dari front-office hingga back-office.

---

## 🏗️ Arsitektur Inti
- **Multi-Tenancy:** Isolasi data aman antar cabang/restoran dengan database tunggal.
- **RBAC (Role Based Access Control):** Pengaturan hak akses granular untuk berbagai peran staff.
- **Real-time Engine:** Didukung oleh Laravel Reverb untuk sinkronisasi data instan.

---

## 🚀 Daftar 44 Fitur Utama (Master List untuk Proposal)

### 🛒 A. Ordering & Customer Experience (7 Fitur)
1. **QR Order Mandiri:** Pelanggan memesan langsung dari meja via browser tanpa perlu download aplikasi. Mengurangi beban kerja pelayan hingga 40%.
2. **Smart Upsell & Bundle:** Sistem secara cerdas menawarkan menu pendamping (misal: "tambah minum?") atau paket hemat saat checkout untuk meningkatkan *Average Order Value* (AOV).
3. **Catatan Per-Item:** Pelanggan bisa menambahkan request khusus (misal: "tanpa bawang", "pedas level 5") pada tiap menu yang dipilih.
4. **Stock Guard Real-time:** Mencegah pelanggan memesan menu yang stok bahan bakunya sedang habis di gudang secara real-time.
5. **Live Order Tracking:** Tautan WhatsApp unik agar pelanggan bisa memantau status pesanan (dimasak, siap, atau diantar) secara live.
6. **Public Digital Receipt:** Nota digital yang bisa diakses selamanya oleh pelanggan via WhatsApp, mendukung gerakan *paperless*.
7. **Table Reservation:** Fitur booking meja online terintegrasi dengan WhatsApp, memungkinkan pelanggan mengamankan tempat sebelum datang ke lokasi.

### 🍳 B. Kitchen Management (4 Fitur)
8. **KDS Real-time WebSocket:** Layar dapur digital yang memperbarui antrean pesanan secara instan milidetik tanpa perlu refresh halaman.
9. **Item Readiness Gating:** Memastikan koki melakukan konfirmasi kematangan untuk setiap item pesanan sebelum dialihkan ke bagian penyajian.
10. **Tampilan Addon & Catatan:** Detail modifikasi pesanan (ekstra keju, dll) ditampilkan dengan visual kontras di KDS agar dapur tidak salah masak.
11. **Waiter Call System:** Tombol digital panggil pelayan atau minta bill di HP pelanggan yang langsung memberi notifikasi ke dashboard staff.

### 💰 C. Point of Sale - POS (10 Fitur)
12. **POS Fullscreen Canggih:** Antarmuka kasir yang dioptimalkan untuk kecepatan tekan, mendukung ribuan transaksi per hari tanpa lag.
13. **Split Bill (Amount & Item):** Fleksibilitas pembayaran grup; pelanggan bisa patungan bayar total atau bayar per menu yang dimakan saja.
14. **Refund & Stock Reversal:** Pembatalan transaksi kasir secara aman yang otomatis mengembalikan stok bahan baku ke gudang dan poin member.
15. **Cash Drawer Integration:** Laci kasir hanya akan terbuka otomatis saat transaksi tunai selesai diproses menggunakan protokol ESC/POS.
16. **Auto-Close Cashier:** Penutupan sesi kasir otomatis setelah jam operasional berakhir untuk menjaga kedisiplinan pelaporan harian.
17. **Quick Launch Hub:** Halaman beranda cepat yang membagi akses staff ke mode KDS, POS, Layanan, atau Kiosk hanya dalam satu klik.
18. **Multi-Payment & Tax Settings:** Konfigurasi dinamis untuk Pajak PB1, Service Charge, dan platform fee yang otomatis dikalkulasi sistem.
19. **POS Anti-Lag (Offline Mode):** Transaksi tetap berjalan meski internet putus; data akan sinkron otomatis saat koneksi kembali stabil.
20. **EDC Integration:** Pencatatan mesin kartu debit/kredit manual lengkap dengan hitungan potongan MDR per bank untuk akurasi pendapatan bersih.
21. **Queue Management System:** Layar TV pemanggil antrean otomatis (Text-to-Speech) yang terintegrasi dengan mesin pendaftaran mandiri (Kiosk).

### 🏦 D. Finance & Accounting (7 Fitur)
22. **Buku Kas & Ledger Otomatis:** Jurnal mutasi saldo setiap restoran yang merinci transaksi masuk, keluar, pajak, dan biaya admin secara transparan.
23. **Bulk Stock Converter:** Kalkulator cerdas untuk mengonversi harga beli grosir (karung/dus) menjadi HPP (Harga Pokok Penjualan) per gram/mililiter secara akurat.
24. **Sistem Withdraw Dana:** Mekanisme penarikan dana dari saldo restoran ke rekening bank pemilik dengan sistem approval yang aman.
25. **Expense Management (P&L):** Pencatatan biaya tetap dan biaya variabel (sewa, gaji, listrik) untuk menghasilkan laporan Rugi Laba bulanan yang valid.
26. **Pajak & Biaya Tambahan:** Pengelolaan pajak restoran (PB1) dan biaya layanan tambahan yang dapat diaktifkan atau dinonaktifkan per cabang.
27. **Admin Fee Withdraw:** Kemampuan admin pusat untuk menetapkan biaya administrasi per transaksi paitout (tarik dana) sebagai sumber revenue platform.
28. **Invoice & Struk Thermal:** Generator otomatis untuk dokumen PDF invoice subscription dan struk belanja thermal (58mm/80mm).

### 📊 E. Analytics & Business Intelligence (7 Fitur)
29. **Sales Report Lengkap:** Analisis harian hingga tahunan dengan grafik tren pendapatan yang memisahkan profit kotor dan profit bersih.
30. **Dashboard HQ (Franchise):** Layar monitor pusat bagi pemilik brand untuk mengawasi performa omzet semua cabang dalam satu tampilan layar.
31. **Menu Engineering (BI):** Klasifikasi menu menggunakan matriks "Stars, Plowhorses, Puzzles, Dogs" untuk menentukan strategi harga dan promosi menu.
32. **Staff & Kitchen Analytics:** Laporan performa staff dapur (rata-rata waktu masak) dan staff kasir untuk evaluasi KPI secara objektif.
33. **Analisis Stok & Kerugian:** Laporan bahan baku yang paling banyak terbuang (wastage) dan prediksi kebutuhan restock bahan baku.
34. **Customer Analytics:** Membedah profil pelanggan, frekuensi kunjungan, dan *Lifetime Value* pelanggan untuk program CRM.
35. **Food Cost & Recipe Insight:** Menghitung margin keuntungan setiap piring makanan berdasarkan harga bahan baku pasar yang fluktuatif.

### 📱 F. Marketing & Infrastructure (9 Fitur)
36. **Self-Service Kiosk:** Layar sentuh mandiri di lobi restoran untuk pelanggan memesan dan membayar langsung tanpa bantuan kasir.
37. **Member Loyalty & Tiering:** Sistem peringkat member (Silver, Gold, Platinum) dengan diskon otomatis menyesuaikan tingkat loyalitas pelanggan.
38. **WhatsApp Gateway & CRM:** Integrasi pesan otomatis untuk kirim nota, OTP login, dan broadcast promo produk baru langsung ke WhatsApp pelanggan.
39. **Email Broadcast System:** Pengiriman kampanye pemasaran berupa newsletter visual menarik dengan penjadwalan otomatis.
40. **PWA (Installable Website):** Website restoran yang bisa diinstal ke layar utama HP pelanggan tanpa lewat App Store, menghemat memori HP pelanggan.
41. **Advanced Role & Permission:** Keamanan tingkat tinggi dengan izin akses berbeda-beda untuk setiap jabatan staff restoran.
42. **Multi-Restaurant HQ:** Kemampuan mengelola ribuan cabang hanya dengan satu identitas login, mendukung ekspansi bisnis skala besar.
43. **Priority Support & Guide:** Sistem tiket bantuan teknis dan video panduan penggunaan langsung di dalam dashboard aplikasi.
44. **Draggable & Personal Dashboard:** Personalisasi tampilan dashboard utama per user menggunakan antarmuka seret-dan-lepas (Drag & Drop) yang user-friendly.

---

## 🛠️ Tech Stack Dasar
- **Backend:** Laravel 11.x
- **Admin Panel:** Filament v3.x
- **Real-time:** Laravel Reverb (WebSockets)
- **Frontend Interactivity:** Livewire, Alpine.js, SortableJS.
- **Database:** MySQL 8.0+

---
*Dokumen ini merupakan aset intelektual Dineflo v2. Terakhir Diperbarui: 11 April 2026.*

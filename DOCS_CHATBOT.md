# README - Dineflo Smart Chatbot System

Dokumen ini berisi informasi lengkap mengenai sistem asisten virtual (Chatbot) yang terintegrasi pada landing page Dineflo. Sistem ini dirancang bukan hanya sebagai elemen visual, tetapi sebagai alat otomatisasi pemasaran (Marketing Automation) yang efisien untuk menjaring calon pelanggan potensial.

---

# Panduan Teknis: Dineflo Smart Chatbot v2

Dineflo Smart Chatbot adalah fitur asisten penjualan otomatis yang dirancang untuk menangkap data calon pelanggan (*lead generation*) secara interaktif sebelum mengarahkan mereka langsung ke WhatsApp tim penjualan.

## 🚀 Fitur Utama
1. **WhatsApp Style UI**: Tampilan gelembung chat, ekor (*tail*) yang presisi, latar belakang *doodle* dinamis, dan animasi halus yang meniru pengalaman WhatsApp asli.
2. **Captured Data**: Secara otomatis meminta Nama, Nomor WhatsApp, Email, dan Alasan Chat/Tujuan Konsultasi.
3. **Delayed Realism**: Simulasi pengetikan asinkron (efek "Nadia sedang mengetik") untuk memberikan sentuhan personal dan manusiawi.
4. **Auto-Scroll Intelijen**: Area chat akan otomatis bergeser ke bawah setiap ada pesan baru tanpa perlu scroll manual.
5. **Full Admin Control**: Seluruh pesan sapaan, avatar bot, hingga latar belakang chat dapat diatur melalui menu **Settings > Chatbot** di Panel Admin Filament.
6. **Smart Redirect**: Redirect ke WhatsApp menggunakan tab baru (`_blank`) dengan pesan yang sudah terisi otomatis sesuai context pembicaraan.

## 🛠️ Alur Kerja Percakapan
1. **Step 0**: Menampilkan Pesan Sapaan Awal dari Admin.
2. **Step 1**: Menanyakan **Nama** pengguna.
3. **Step 2**: Menanyakan **Nomor WhatsApp** (dengan validasi angka 10-15 digit).
4. **Step 3**: Menanyakan **Email** (dengan format validasi `@`).
5. **Step 4**: Menanyakan **Tujuan/Alasan Chat** (disimpan sebagai isi pesan).
6. **Step 5**: Menyimpan data ke database `contact_submissions` dan melakukan redirect ke WhatsApp.

## 📂 Komponen Terkait
- **Livewire**: `App\Livewire\Public\Chatbot.php` (Logika & Validasi)
- **View**: `resources/views/livewire/public/chatbot.blade.php` (UI & Alpine.js)
- **Settings**: `App\Settings\GeneralSettings.php` (Penyimpanan Konfigurasi)
- **Database**: Tabel `contact_submissions` (Penyimpanan Leads)

## ⚙️ Cara Pengaturan (Admin Panel)
Buka menu **General Settings** > Tab **Chatbot** untuk mengatur:
- **Status Aktif**: Menghidupkan/Mematikan chatbot di seluruh landing page.
- **Identitas Bot**: Nama bot dan Avatar/Foto profil bot.
- **Visual**: Background Chat Pattern (disarankan gambar pola transparan/PNG).
- **Conversational Scripts**: Kustomisasi seluruh pesan tanya jawab agar sesuai dengan gaya bahasa brand Anda.

---
*Dokumentasi ini dibuat untuk memastikan integritas fitur dan memudahkan pengembangan di masa depan.*

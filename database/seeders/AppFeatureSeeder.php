<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AppFeature;
use Illuminate\Support\Str;

class AppFeatureSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate existing features to re-seed with high-quality descriptions
        AppFeature::truncate();

        $sections = [
            ['tab' => 'order', 'cards' => [
                [
                    'QR Order Mandiri', 
                    'Pelanggan scan QR di meja, pilih menu, bayar via HP tanpa antri kasir.', 
                    ['QR unik per meja', 'Pembayaran QRIS/E-Wallet', 'Notifikasi real-time ke dapur', 'Status pesanan live'], 
                    'Premium',
                    '<h3>Revolusi Cara Pesan di Restoran Anda</h3>
                    <p>Hilangkan hambatan komunikasi antara pelanggan dan pelayan. Dengan <b>QR Order Mandiri</b>, pelanggan tidak perlu lagi menunggu pelayan datang membawa buku menu atau berteriak memanggil saat ingin menambah pesanan.</p>
                    <ul>
                        <li><b>Meningkatkan Turnover Meja:</b> Pesanan masuk lebih cepat, makanan keluar lebih cepat, pelanggan selesai lebih cepat.</li>
                        <li><b>Efisiensi SDM:</b> Restoran Anda tetap bisa beroperasi maksimal meski dengan jumlah staff yang terbatas.</li>
                        <li><b>Akurasi Pesanan:</b> Kesalahan input pesanan oleh staff berkurang hingga 100% karena pelanggan memilih sendiri varian dan catatannya.</li>
                    </ul>'
                ],
                [
                    'Smart Upsell & Bundle', 
                    'Rekomendasi "Pasangan Terbaik" + Quick-Add Bundle di keranjang pelanggan.', 
                    ['Pasangan terbaik reciprocal', 'Quick-configure overlay', 'Bundle cart button dinamis', 'Bundle summary pre-checkout'], 
                    'Premium',
                    '<h3>Tingkatkan Rata-rata Nilai Order (AOV) Tanpa Usaha Ekstra</h3>
                    <p>Teknologi Smart Upsell kami bekerja seperti pelayan paling handal yang selalu menawarkan pendamping yang pas untuk setiap hidangan.</p>
                    <ul>
                        <li><b>Rekomendasi Relevan:</b> Jika pelanggan memesan Steak, sistem secara otomatis menawarkan Red Wine atau Side Dish premium.</li>
                        <li><b>Bundle Packages:</b> Buat paket hemat (Combo) yang muncul hanya saat item tertentu ditambahkan ke keranjang.</li>
                        <li><b>Psikologi Belanja:</b> Desain overlay yang menarik mendorong pelanggan untuk menambah "sedikit lagi" item ke pesanan mereka.</li>
                    </ul>'
                ],
                [
                    'Catatan Per-Item', 
                    'Pelanggan tambah catatan khusus tiap menu (misal: tidak pedas, tanpa bawang).', 
                    ['Muncul di KDS & struk thermal', 'Tampil di WhatsApp invoice', 'Dukungan di QR & Kiosk', 'Tersedia di Kasir POS'], 
                    'Standar',
                    '<h3>Personalisasi Pesanan untuk Kepuasan Maksimal</h3>
                    <p>Setiap pelanggan memiliki selera yang unik. Fitur <b>Catatan Per-Item</b> memungkinkan restoran Anda memberikan sentuhan personal pada setiap hidangan tanpa risiko salah komunikasi.</p>
                    <ul>
                        <li><b>Detail yang Akurat:</b> Dari tingkat kepedasan hingga pantangan alergi, semua catatan pelanggan akan langsung tersalurkan ke departemen terkait tanpa perlu teriakan atau memo kertas manual.</li>
                        <li><b>Terintegrasi ke Dapur:</b> Catatan akan muncul dengan jelas di layar KDS (Kitchen Display System), membantu koki menyajikan makanan persis seperti yang diinginkan.</li>
                        <li><b>Bukti di Struk:</b> Catatan juga tercetak di struk belanja dan invoice WhatsApp, memberikan rasa tenang kepada pelanggan bahwa pesanan mereka telah dicatat dengan benar.</li>
                    </ul>'
                ],
                [
                    'Stock Guard Real-time', 
                    'Validasi stok sebelum & saat checkout. Mencegah order melebihi stok tersedia.', 
                    ['Hard-lock lockForUpdate()', 'Error inline + auto-dismiss', 'Out-of-stock visual di POS', 'Race condition prevention'], 
                    'Standar',
                    '<h3>Sistem Inventori Tanpa Celah</h3>
                    <p>Kehilangan potensi penjualan karena stok salah hitung adalah masalah masa lalu. Dengan <b>Stock Guard Real-time</b>, sistem menjaga setiap gram bahan baku Anda dengan ketat.</p>
                    <ul>
                        <li><b>Akurasi Mutlak:</b> Validasi stok dilakukan dua kali; saat pelanggan memilih menu dan sesaat sebelum pembayaran diproses.</li>
                        <li><b>Otomasi Stok:</b> Begitu transaksi sukses, stok langsung berkurang di gudang pusat tanpa input manual.</li>
                        <li><b>Peringatan Dini:</b> Sistem akan memberitahu kasir atau pelanggan secara visual jika stok sudah mencapai batas kritis.</li>
                    </ul>'
                ],
                [
                    'Live Order Tracking', 
                    'Pelanggan pantau status pesanan real-time via link WhatsApp tanpa login.', 
                    ['Hash URL unik per order', 'Animated status badges', 'Update otomatis Reverb', 'Tanpa akun pelanggan'], 
                    'Standar',
                    '<h3>Transparansi yang Menenangkan Pelanggan</h3>
                    <p>Menunggu adalah bagian paling membosankan bagi pelanggan. Fitur <b>Live Tracking</b> mengubah kecemasan menjadi antusiasme dengan memberikan info real-time.</p>
                    <ul>
                        <li><b>Tanpa Ribet:</b> Pelanggan tidak perlu download aplikasi atau login. Cukup klik link unik dari WhatsApp mereka.</li>
                        <li><b>Visualisasi Status:</b> Indikator status yang dinamis menunjukkan apakah pesanan masih di antrean, sedang dimasak, atau sudah siap disajikan.</li>
                        <li><b>Efisiensi Staff:</b> Berkurangnya pelanggan yang bertanya "Pesanan saya sudah sampai mana?" ke pelayan Anda.</li>
                    </ul>'
                ],
                [
                    'Public Digital Receipt', 
                    'Nota digital akses tanpa login via link WhatsApp setelah transaksi selesai.', 
                    ['Link unik per order', 'Detail item + payment', 'Accessible tanpa akun', 'Branding restoran'], 
                    'Standar',
                    '<h3>Bukti Pembayaran Modern & Eco-Friendly</h3>
                    <p>Tinggalkan tumpukan kertas yang mudah hilang. Berikan pelanggan kemudahan akses nota digital kapan saja dan di mana saja.</p>
                    <ul>
                        <li><b>Akses Abadi:</b> Nota tersimpan aman di cloud dan bisa diunduh sebagai PDF oleh pelanggan untuk keperluan reimbursment mereka.</li>
                        <li><b>Personal branding:</b> Nota digital tetap menampilkan logo, alamat, dan pesan khusus dari restoran Anda secara profesional.</li>
                    </ul>'
                ],
                [
                    'Table Reservation', 
                    'Pesan meja dari rumah lewat website restoran. Terintegrasi ke WhatsApp. ', 
                    ['Online booking 24/7', 'Otomatis cek jam operasional', 'Live Tracking via WhatsApp', 'Table assignment di admin'], 
                    'Premium',
                    '<h3>Kelola Reservasi Tanpa Perlu Angkat Telepon</h3>
                    <p>Berikan kemudahan bagi pelanggan setia Anda untuk mengamankan kursi favorit mereka kapan saja, bahkan saat restoran sedang tutup.</p>
                    <ul>
                        <li><b>Booking 24/7:</b> Pelanggan bisa melakukan reservasi kapan saja melalui halaman website restoran Anda tanpa perlu menunggu staff menjawab telepon.</li>
                        <li><b>Validasi Pintar:</b> Sistem secara otomatis mengecek jam operasional dan hari libur restoran Anda, mencegah adanya pesanan di luar jam kerja.</li>
                        <li><b>Live Status Tracking:</b> Pelanggan mendapatkan link unik untuk memantau apakah reservasi mereka sudah dikonfirmasi atau meja sudah siap digunakan.</li>
                    </ul>'
                ],
            ]],
            ['tab' => 'kitchen', 'cards' => [
                [
                    'KDS Real-time WebSocket', 
                    'Papan digital dapur live via Laravel Reverb tanpa refresh halaman.', 
                    ['Live update WebSocket', 'Kolom Incoming → Cooking → Ready', 'Grouping status confirmed & pending', 'Auto-refresh fallback'], 
                    'Premium',
                    '<h3>Digitalisasi Dapur: Tinggalkan Kertas Thermal yang Boros</h3>
                    <p><b>Kitchen Display System (KDS)</b> adalah jantung dari efisiensi dapur restoran modern. Tidak ada lagi tiket pesanan yang hilang, basah, atau tidak terbaca.</p>
                    <ul>
                        <li><b>Sinkronisasi Instan:</b> Begitu pelanggan bayar atau kasir klik order, pesanan langsung muncul di layar dapur dalam milidetik.</li>
                        <li><b>Kolom Alur Kerja:</b> Kelola pesanan berdasarkan status: Masuk, Sedang Dimasak, hingga Siap Disajikan.</li>
                        <li><b>Visualisasi Prioritas:</b> Pesanan yang sudah terlalu lama menunggu akan berubah warna untuk memperingatkan koki.</li>
                    </ul>'
                ],
                [
                    'Item Readiness Gating', 
                    'Koki centang tiap item sebelum "Mark Ready". Mencegah pesanan setengah jadi kirim.', 
                    ['Checkbox per item di Cooking', 'Tombol terkunci sampai semua centang', 'Visual progress per order', 'Konfirmasi siap per-menu'], 
                    'Premium',
                    '<h3>Disiplin Dapur untuk Kualitas Hidangan</h3>
                    <p>Pastikan setiap pesanan keluar dari dapur dalam kondisi lengkap. Tidak ada lagi kejadian minuman sudah di meja tapi makanan masih tertinggal di dapur.</p>
                    <ul>
                        <li><b>Kendali Penuh Koki:</b> Sistem mewajibkan staff dapur memberikan konfirmasi untuk SETIAP item yang ada dalam satu nomor pesanan.</li>
                        <li><b>Visual Progress:</b> Head Chef bisa melihat persentase kesiapan pesanan secara visual (misal: 3 dari 5 menu sudah siap).</li>
                        <li><b>Minimalisir Komplain:</b> Mengurangi risiko pesanan yang dikirim ke meja dalam kondisi tidak lengkap.</li>
                    </ul>'
                ],
                [
                    'Tampilan Addon & Catatan', 
                    'Semua tambahan, modifikasi, dan catatan pelanggan tampil di semua kolom KDS.', 
                    ['Addon display semua kolom', 'Catatan khusus per item', 'Info alergi pelanggan', 'Timer order terlama'], 
                    'Standar', 
                    '<h3>Detail Pesanan yang Jelas untuk Dapur Anda</h3>
                    <p>Jangan biarkan pesanan pelanggan menjadi teka-teki. Fitur <b>Tampilan Addon & Catatan</b> memastikan setiap modifikasi pesanan terlihat dengan jelas dan kontras di layar dapur Anda.</p>
                    <ul>
                        <li><b>Kontras yang Tinggi:</b> Addon dan catatan diintegrasikan ke dalam tiap item menu dengan gaya visual yang berbeda (bold/italic) agar staff dapur tidak melewatkan permintaan khusus.</li>
                        <li><b>Informasi Alergi:</b> Jika pelanggan mencantumkan alergi, sistem akan memberikan penanda khusus yang lebih mencolok untuk keamanan ekstra.</li>
                        <li><b>Timer Order Terlama:</b> Sistem secara otomatis menghitung berapa lama pesanan telah berada di dapur. Pesanan dengan durasi terlama akan mendapatkan perhatian khusus untuk menjaga kepuasan pelanggan.</li>
                        <li><b>Sinkronisasi Semua Kolom:</b> Baik di kolom "Masuk", "Memasak", maupun "Siap", catatan tersebut akan tetap menempel pada item menu agar pelayan tidak salah antar saat disajikan.</li>
                    </ul>'
                ],
                [
                    'Waiter Call System', 
                    'Pelanggan tekan tombol panggil pelayan. Staff terima notifikasi real-time.', 
                    ['Tombol panggil di menu pelanggan', 'Broadcast event real-time', 'Staff notification panel', 'Status pending/responded'], 
                    'Standar',
                    '<h3>Komunikasi Staff yang Lebih Responsif</h3>
                    <p>Hilangkan kebiasaan pelanggan melambaikan tangan dengan frustrasi. Berikan cara yang lebih berkelas untuk meminta bantuan pelayan.</p>
                    <ul>
                        <li><b>Notifikasi Instan:</b> Saat pelanggan menekan tombol di HP mereka melalui menu QR, dashboard pelayan akan bergetar atau berbunyi secara real-time.</li>
                        <li><b>Identitas Meja & Keperluan:</b> Sistem langsung memberitahu pelayan meja nomor berapa yang membutuhkan bantuan dan apa yang mereka butuhkan (panggil pelayan atau minta bill).</li>
                        <li><b>Tracking Respon:</b> Manajer bisa melihat rata-rata waktu yang dibutuhkan staff untuk merespon panggilan pelanggan, membantu dalam evaluasi performa staff.</li>
                    </ul>'
                ],
            ]],
            ['tab' => 'pos', 'cards' => [
                [
                    'POS Fullscreen Canggih', 
                    'Tampilan kasir compact grid dengan sidebar cart, multi-payment dan manajemen pesanan.', 
                    ['Multi-payment: Cash, QRIS, Midtrans', 'Quick Order & Takeaway mode', 'Out-of-stock visual guard', 'Catatan per item'], 
                    'Standar',
                    '<h3>Kasir Super Cepat, Pelanggan Puas</h3>
                    <p>Point of Sale (POS) Dineflo dirancang untuk kecepatan tinggi tanpa mengorbankan fungsionalitas. Cocok untuk jam sibuk dengan antrean panjang.</p>
                    <ul>
                        <li><b>Multi-Payment Ready:</b> Terima pembayaran Tunai, QRIS, maupun Kartu dalam satu alur kerja yang mulus.</li>
                        <li><b>Inventory Lock:</b> Kasir tidak akan bisa menjual item yang stoknya sudah habis di sistem, menghindari kekecewaan pelanggan.</li>
                        <li><b>Takeaway Mode:</b> Beralih antara mode makan di tempat (Dine-in) atau dibawa pulang (Takeaway) hanya dengan satu klik.</li>
                    </ul>'
                ],
                [
                    'Split Bill (By Amount & Item)', 
                    'Bayar tagihan patungan — bagi nominal atau per item yang dipilih customer.', 
                    ['Split by Amount', 'Split by Item (Empire exclusive)', 'Multi-payment per order', 'Auto-save saat pindah mode'], 
                    'Premium',
                    '<h3>Solusi Pembayaran Grup yang Menyenangkan</h3>
                    <p>Membayar tagihan bersama teman tidak lagi menjadi beban bagi kasir Anda. Fitur <b>Split Bill</b> menangani perhitungan rumit secara otomatis.</p>
                    <ul>
                        <li><b>Bagi Rata (Amount):</b> Pembayaran dibagi berdasarkan total nominal secara adil.</li>
                        <li><b>Bagi per Item:</b> Pelanggan hanya membayar menu yang mereka makan saja. Sangat membantu untuk akurasi pesanan grup besar.</li>
                        <li><b>Multi-Payment Per Sesi:</b> Satu grup bisa membayar dengan kombinasi Tunai, QRIS, dan Kartu sekaligus.</li>
                    </ul>'
                ],
                [
                    'Refund & Stock Reversal', 
                    'Batal transaksi dengan pemulihan stok & poin member otomatis ke database.', 
                    ['Refund per-item atau total', 'Otomatis restore stok bahan', 'Point reversal member', 'Audit trail di Ledger harian'], 
                    'Standar',
                    '<h3>Kelola Pembatalan dengan Cepat dan Akurat</h3>
                    <p>Kesalahan input atau pembatalan pesanan adalah hal yang lumrah. Fitur <b>Refund & Stock Reversal</b> menangani proses ini secara otomatis tanpa merusak keseimbangan inventori Anda.</p>
                    <ul>
                        <li><b>Otomasi Stok:</b> Saat pesanan dibatalkan, sistem secara cerdas akan mengembalikan stok bahan baku ke gudang pusat sehingga data HPP Anda tetap akurat.</li>
                        <li><b>Loyalty Sync:</b> Jika transaksi menggunakan poin member, poin tersebut akan dikembalikan secara otomatis ke akun pelanggan saat refund dilakukan.</li>
                        <li><b>Jejak Audit:</b> Setiap aktivitas refund akan tercatat di log keuangan harian, lengkap dengan nama staff yang melakukan otorisasi.</li>
                    </ul>'
                ],
                [
                    'Cash Drawer Integration', 
                    'Laci kasir buka otomatis saat transaksi via printer thermal ESC/POS.', 
                    ['ESC/POS thermal print', 'Cash Drawer Log (siapa buka laci)', 'Bluetooth & USB printer', 'Struk 58mm & 80mm'], 
                    'Standar',
                    '<h3>Keamanan dan Kecepatan di Meja Kasir</h3>
                    <p>Tingkatkan profesionalisme operasional kasir Anda dengan integrasi laci kasir (Cash Drawer) yang mulus dengan printer thermal Anda.</p>
                    <ul>
                        <li><b>Buka Otomatis:</b> Laci kasir akan terbuka secara otomatis hanya saat transaksi tunai selesai diproses, meningkatkan kecepatan staff saat melayani kembalian.</li>
                        <li><b>Log Keamanan:</b> Sistem mencatat setiap kali laci dibuka, memberikan lapisan keamanan tambahan untuk mencegah akses yang tidak sah ke uang tunai.</li>
                        <li><b>Kompatibilitas Luas:</b> Mendukung printer thermal standar industri berukuran 58mm maupun 80mm via kabel RJ11 standar.</li>
                    </ul>'
                ],
                [
                    'Auto-Close Cashier', 
                    'Sistem tutup sesi kasir otomatis 1 jam setelah jam operasional berakhir.', 
                    ['Auto rekonsiliasi Closing=Expected', 'Banner peringatan jam operasional', 'Closing report otomatis', 'Riwayat sesi kasir'], 
                    'Standar',
                    '<h3>Penutupan Buku Tanpa Repot</h3>
                    <p>Lupakan laporan harian yang terlambat atau lupa dikerjakan. Fitur <b>Auto-Close</b> memastikan disiplin pelaporan keuangan restoran Anda terjaga secara otomatis.</p>
                    <ul>
                        <li><b>Otomasi Laporan:</b> Sistem akan menutup sesi kasir secara otomatis (misal: 1 jam setelah jam operasional tutup), menghitung total pendapatan, dan mengirimkan ringkasannya ke admin.</li>
                        <li><b>Rekonsiliasi Pintar:</b> Menampilkan perbandingan antara pendapatan yang diharapkan (dari sistem) dengan hasil input manual, memudahkan deteksi selisih sejak dini.</li>
                        <li><b>Banner Peringatan:</b> Memberikan notifikasi kepada kasir yang masih aktif saat mendekati waktu penutupan otomatis agar mereka segera menyelesaikan transaksi terakhir.</li>
                    </ul>'
                ],
                [
                    'Quick Launch Hub', 
                    'Halaman /quick-launch — 5 launcher card: KDS, Service, POS, Kiosk, Mini Website.', 
                    ['Buka di tab baru', 'Dinamis per role & feature', 'Card permission-based', 'Akses cepat semua tool'], 
                    'Standar',
                    '<h3>Pusat Komando dalam Satu Genggaman</h3>
                    <p>Navigasi yang rumit adalah hambatan. <b>Quick Launch Hub</b> adalah halaman beranda pintar yang memberikan akses instan ke semua alat yang Anda butuhkan.</p>
                    <ul>
                        <li><b>Akses Berbasis Peran:</b> Tampilan yang muncul di Hub ini menyesuaikan dengan izin akses staff (misal: Chef hanya melihat KDS, Kasir melihat POS).</li>
                        <li><b>Dinamis:</b> Cukup satu klik untuk berpindah dari sistem KDS dapur ke Dashboard Analitik tanpa perlu login ulang atau mencari Menu di sidebar.</li>
                        <li><b>Optimal untuk Tablet:</b> Desain kartu yang besar memudahkan staff untuk menekan tombol dengan cepat bahkan di layar sentuh yang kecil.</li>
                    </ul>'
                ],
                [
                    'Multi-Payment & Tax Settings', 
                    'Atur metode, pajak PB1, service charge, dan platform fee per restoran.', 
                    ['Toggle metode pembayaran', 'Sub-opsi bayar dulu vs langsung KDS', 'Pajak PB1 & service charge dinamis', 'JSON additional fees'], 
                    'Standar',
                    '<h3>Fleksibilitas Pembayaran dan Kepatuhan Pajak</h3>
                    <p>Sesuaikan cara Anda menerima uang dan kelola kewajiban pajak restoran dengan sangat mudah melalui panel konfigurasi yang fleksibel.</p>
                    <ul>
                        <li><b>Pajak PB1 & Service Charge:</b> Aktifkan atau nonaktifkan pajak restoran dan biaya layanan hanya dengan satu tombol. Persentase perhitungan akan otomatis diaplikasikan ke seluruh channel (POS, QR, Kiosk).</li>
                        <li><b>Metode Pembayaran:</b> Pilih metode apa saja yang ingin Anda tawarkan (Tunai, QRIS, Transfer Bank, atau Kartu Kredit) sesuai dengan ketersediaan di lokasi Anda.</li>
                        <li><b>Urutan Alur Kerja:</b> Atur apakah pesanan harus dibayar terlebih dahulu (Pre-paid) baru masuk dapur, atau boleh makan dulu baru bayar (Post-paid).</li>
                    </ul>'
                ],
                [
                    'POS Anti-Lag (Offline Mode)', 
                    'Transaksi tetap lancar meskipun koneksi internet terputus secara tiba-tiba.', 
                    ['Penyimpanan lokal IndexedDB', 'Sinkronisasi otomatis saat online', 'Validasi stok offline', 'Nota offline generator'], 
                    'Standar',
                    '<h3>Andalkan Bisnis Anda pada Ketangguhan Sistem</h3>
                    <p>Kehilangan koneksi internet di tengah jam sibuk adalah mimpi buruk, tapi tidak bagi pengguna Dineflo. Sistem kami tetap bekerja saat yang lain terhenti.</p>
                    <ul>
                        <li><b>Simpan Lokal:</b> Transaksi disimpan sementara di penyimpanan lokal browser yang aman.</li>
                        <li><b>Sinkronisasi Cerdas:</b> Begitu internet kembali menyala, sistem akan mengirimkan semua data transaksi ke server secara otomatis tanpa Anda sadari.</li>
                        <li><b>Validasi Tetap Jalan:</b> Kasir masih bisa melihat stok dan memberikan struk meskipun sedang offline.</li>
                    </ul>'
                ],
                [
                    'EDC Integration', 
                    'Koneksikan mesin EDC bank Anda dengan kalkulasi MDR otomatis di kasir.', 
                    ['Manajemen MDR Fee (%)', 'Dukungan Multi-Bank', 'Input No. Trace/Referensi', 'Net Revenue otomatis terpotong fee'], 
                    'Premium',
                    '<h3>Rekonsiliasi Bank Jadi Lebih Mudah & Akurat</h3>
                    <p>Jangan biarkan selisih serupiah pun antara mutasi bank dengan laporan kasir Anda. Fitur <b>Integrasi EDC</b> dirancang untuk akurasi finansial tingkat tinggi.</p>
                    <ul>
                        <li><b>Kalkulasi MDR Otomatis:</b> Sistem otomatis menghitung potongan biaya bank (MDR) secara real-time saat transaksi, sehingga angka "Net Revenue" di dashboard Anda adalah uang bersih yang akan masuk ke rekening.</li>
                        <li><b>Minimalisir Human Error:</b> Kasir wajib menginput nomor Trace/Referensi sebagai bukti validasi transaksi EDC, memudahkan verifikasi saat audit harian.</li>
                        <li><b>Configurable Bank List:</b> Bebas tambahkan bank apa saja (BCA, Mandiri, BRI, dll) dengan biaya MDR yang berbeda-beda sesuai kebijakan bank masing-masing.</li>
                    </ul>'
                ],
                [
                    'Queue Management System', 
                    'Layar TV panggil antrean, pendaftaran di Kiosk, pendaftaran online, dan integrasi meja.', 
                    ['Panggil antrean (Text-to-Speech)', 'Display TV panggil real-time', 'Ambil antrean di Kiosk', 'Booking Online via Web Profile'], 
                    'Premium',
                    '<h3>Manajemen Antrean Kelas Dunia</h3>
                    <p>Hilangkan kerumunan di depan kasir. Atur alur masuk pelanggan Anda dengan sistem antrean yang profesional dan terintegrasi.</p>
                    <ul>
                        <li><b>Panggilan Otomatis:</b> Sistem menggunakan suara (Text-to-Speech) untuk memanggil nomor antrean, memberikan kesan modern.</li>
                        <li><b>TV Display:</b> Tampilkan nomor antrean yang sedang dilayani di layar TV restoran agar pelanggan bisa menunggu dengan tenang sambil duduk.</li>
                        <li><b>Omni-Channel Queue:</b> Pelanggan bisa mengambil antrean dari rumah (WhatsApp) atau langsung di lokasi melalui mesin Kiosk.</li>
                    </ul>'
                ],
            ]],
            ['tab' => 'finance', 'cards' => [
                [
                    'Buku Kas & Ledger Otomatis', 
                    'Mutasi saldo real-time dengan rincian Gross, Biaya Admin Gateway, dan Net Bersih.', 
                    ['Audit trail per transaksi', 'Net Revenue (Eks. Pajak & Fee)', 'Klik rincian dari Dashboard', 'Rekonsiliasi keuangan instan'], 
                    'Standar',
                    '<h3>Keuangan Transparan, Tanpa Selisih Satu Rupiah pun</h3>
                    <p>Fitur <b>Buku Kas (Ledger)</b> kami adalah asisten akuntansi pribadi Anda. Sistem mencatat setiap aliran dana masuk dan keluar secara real-time dengan tingkat akurasi tinggi.</p>
                    <ul>
                        <li><b>Truly Net Revenue:</b> Sistem otomatis memisahkan Pendapatan Kotor (Gross) dari Pajak, Biaya Layanan, dan Biaya Admin Gateway (QRIS/CC). Anda langsung tahu berapa uang bersih yang benar-benar bisa digunakan.</li>
                        <li><b>Audit Trail:</b> Setiap transaksi memiliki jejak digital yang tidak bisa dihapus. Anda bisa melacak siapa kasirnya, lewat pembayaran apa, dan jam berapa dana masuk.</li>
                        <li><b>Rekonsiliasi Otomatis:</b> Tidak perlu lagi menghitung manual di akhir hari. Ledger kami sudah menghitungkan saldo akhir yang seharusnya ada di rekening atau laci kasir Anda.</li>
                    </ul>'
                ],
                [
                    'Bulk Stock Converter', 
                    'Kalkulator otomatis untuk hitung harga porsi dari pembelian karung/grosir.', 
                    ['Input Harga per Karung/Dus', 'Konversi otomatis ke Gram/ml', 'Harga modal base-unit live', 'Estimasi HPP per porsi'], 
                    'Standar',
                    '<h3>Konversi Satuan Grosir ke Porsi dengan Akurasi Tinggi</h3>
                    <p>Kesulitan menghitung harga modal per gram dari pembelian karung atau grosir? <b>Bulk Stock Converter</b> adalah kalkulator cerdas yang menyederhanakan matematika dapur Anda.</p>
                    <ul>
                        <li><b>Input Grosir:</b> Masukkan harga beli satu karung beras atau satu dus bumbu, lalu biarkan sistem menghitungkan modal per gram/mililiter untuk Anda.</li>
                        <li><b>Update Real-time:</b> Perubahan harga bahan baku di pasar akan langsung memperbarui estimasi HPP (Harga Pokok Penjualan) menu Anda secara otomatis.</li>
                        <li><b>Efisiensi Belanja:</b> Membantu manajer pengadaan melihat bahan mana yang lebih menguntungkan dibeli dalam jumlah besar.</li>
                    </ul>'
                ],
                [
                    'Sistem Withdraw Dana', 
                    'Restoran ajukan penarikan saldo ke rekening bank kapan saja dengan transparan.', 
                    ['Form withdraw + riwayat lengkap', 'Smart balance guard anti over-withdraw', 'Approval + konfirmasi transfer + bukti foto', 'Notifikasi status withdraw real-time'], 
                    'Standar',
                    '<h3>Penarikan Pendapatan Digital yang Transparan</h3>
                    <p>Kendalikan arus kas restoran Anda dengan sistem penarikan dana (Withdraw) yang aman, cepat, dan tercatat dengan detail.</p>
                    <ul>
                        <li><b>Smart Balance Guard:</b> Sistem secara otomatis menghitung saldo yang benar-benar siap ditarik (Settled) setelah dipotong biaya-biaya, mencegah kesalahan over-withdraw.</li>
                        <li><b>Riwayat Lengkap:</b> Pantau status penarikan Anda mulai dari "Diajukan", "Diproses", hingga "Berhasil" lengkap dengan bukti transfer dari tim pusat.</li>
                        <li><b>Notifikasi Real-time:</b> Dapatkan kabar langsung melalui WhatsApp ketika dana sudah berhasil ditransfer ke rekening bank restoran Anda.</li>
                    </ul>'
                ],
                [
                    'Expense Management (P&L)', 
                    'Catat pengeluaran operasional untuk laporan laba rugi yang akurat.', 
                    ['Kategori: Sewa, Gaji, Listrik', 'Pembelian bahan baku', 'Dashboard P&L bulanan', 'Net Profit calculation'], 
                    'Standar',
                    '<h3>Kelola Pengeluaran untuk Laba Maksimal</h3>
                    <p>Dapatkan gambaran jernih tentang profitabilitas restoran Anda dengan memantau setiap pengeluaran operasional di satu tempat.</p>
                    <ul>
                        <li><b>Kategorisasi Biaya:</b> Pisahkan pengeluaran untuk Gaji, Sewa, Bahan Baku, dan Utilitas untuk analisis yang lebih tajam.</li>
                        <li><b>Laporan Laba Rugi (P&L):</b> Dashboard otomatis menghitung Net Profit dengan memotong pendapatan kotor Anda dengan rincian pengeluaran yang sudah diinput.</li>
                    </ul>'
                ],
                [
                    'Pajak & Biaya Tambahan', 
                    'Toggle PB1, service charge, platform fee dengan kalkulasi live di checkout.', 
                    ['Tax enabled & persentase dinamis', 'Repeater biaya tambahan Fixed atau persen', 'Kalkulasi live di POS, QR, Kiosk', 'Disimpan sebagai JSON di database'], 
                    'Standar',
                    '<h3>Personalisasi Struktur Biaya Restoran Anda</h3>
                    <p>Atur berbagai biaya tambahan dan pajak dengan fleksibel tanpa perlu bantuan programmer setiap kali ada perubahan kebijakan pemerintah.</p>
                    <ul>
                        <li><b>Kalkulasi Dinamis:</b> Biaya bisa berupa persentase (misal: Pajak 10%) atau nilai tetap (misal: Biaya Takeaway Rp 2.000).</li>
                        <li><b>Transparansi Pelanggan:</b> Semua rincian biaya akan muncul secara jujur di keranjang belanja pelanggan dan struk kasir.</li>
                    </ul>'
                ],
                [
                    'Admin Fee Withdraw', 
                    'Platform fee Dineflo atas setiap penarikan dana (configurable, default 0 gratis).', 
                    ['Persentase dari Global Settings', 'Preview breakdown kalkulasi live', 'Kolom Admin Fee & Net Transfer', 'Default 0% gratis untuk restoran'], 
                    'Standar',
                    '<h3>Biaya Administrasi yang Transparan</h3>
                    <p>Dineflo berkomitmen untuk memberikan layanan yang adil dan terbuka bagi setiap mitra restoran kami.</p>
                    <ul>
                        <li><b>Tanpa Kejutan:</b> Lihat rincian biaya admin (jika ada) secara langsung sebelum Anda menekan tombol tarik dana.</li>
                        <li><b>Kalkulasi Otomatis:</b> Sistem memotong secara otomatis dari saldo pengajuan sehingga dana yang masuk ke rekening Anda adalah nilai bersih yang dijanjikan.</li>
                    </ul>'
                ],
                [
                    'Invoice & Struk Thermal', 
                    'Generate PDF invoice subscription & struk thermal untuk kasir secara otomatis.', 
                    ['Struk thermal 58mm & 80mm', 'PDF invoice subscription', 'Email invoice otomatis', 'Public receipt link pelanggan'], 
                    'Standar',
                    '<h3>Dokumentasi Transaksi Profesional</h3>
                    <p>Sajikan bukti pembayaran yang elegan dan lengkap untuk meningkatkan kepercayaan pelanggan dan kerapian administrasi restoran.</p>
                    <ul>
                        <li><b>Multi-Format:</b> Mendukung cetakan fisik struk kasir melalui printer thermal maupun pengiriman invoice digital secara profesional melalui email atau WhatsApp.</li>
                        <li><b>Desain Bersih:</b> Invoice didesain untuk mudah dibaca, mencantumkan semua rincian item, pajak, dan diskon secara mendetail.</li>
                    </ul>'
                ],
            ]],
            ['tab' => 'analytics', 'cards' => [
                [
                    'Sales Report Lengkap', 
                    'Laporan penjualan dengan filter tanggal, grafik tren, dan statistik lengkap.', 
                    ['Filter harian/mingguan/bulanan', 'Truly Net Revenue yang akurat', 'Top products & categories', 'Export PDF & Excel'], 
                    'Standar',
                    '<h3>Data yang Berbicara, Keputusan yang Bijak</h3>
                    <p><b>Laporan Penjualan</b> bukan sekadar angka. Ini adalah peta jalan bagi bisnis Anda untuk tumbuh lebih besar.</p>
                    <ul>
                        <li><b>Visualisasi Tren:</b> Lihat jam berapa restoran Anda paling ramai dan hari apa yang paling sepi untuk mengatur promo atau jadwal staff secara efektif.</li>
                        <li><b>Truly Net Profit:</b> Laporan ini sudah terintegrasi dengan Ledger, sehingga angka "Pendapatan Bersih" yang Anda lihat adalah uang yang benar-benar profit setelah dipotong biaya-biaya.</li>
                        <li><b>Analisis Produk:</b> Ketahui menu mana yang merupakan "Star" (Laris & Untung Besar) dan mana yang "Dog" (Sepi & Untung Kecil) untuk optimasi menu Anda.</li>
                    </ul>'
                ],
                [
                    'Dashboard HQ (Franchise)', 
                    'Monitor semua cabang dari satu dashboard konsolidasi multi-outlet.', 
                    ['Revenue & Orders multi-outlet', 'Direct branch switching', 'Locked state jika belum subscribe', 'Performance comparison per cabang'], 
                    'Premium',
                    '<h3>Pantau Seluruh Cabang dalam Satu Layar</h3>
                    <p>Untuk pemilik bisnis multi-outlet, <b>Dashboard HQ</b> adalah mata Anda di setiap lokasi. Dapatkan gambaran performa bisnis secara keseluruhan tanpa perlu berpindah aplikasi.</p>
                    <ul>
                        <li><b>Konsolidasi Data:</b> Bandingkan performa antar cabang dalam satu grafik untuk melihat outlet mana yang paling produktif dan mana yang butuh perhatian lebih.</li>
                        <li><b>Switching Instan:</b> Berpindah ke dashboard detail cabang tertentu hanya dalam satu klik tanpa perlu login ulang.</li>
                    </ul>'
                ],
                [
                    'Menu Engineering (BI)', 
                    'Kategorisasi menu: Stars, Plowhorses, Puzzles, Dogs untuk keputusan strategis bisnis.', 
                    ['Auto-kalkulasi HPP per porsi', 'Kontribusi laba bersih per menu', 'GP percentage otomatis', 'Rekomendasi evaluasi menu'], 
                    'Premium',
                    '<h3>Gunakan Kecerdasan Bisnis untuk Optimasi Menu</h3>
                    <p>Ubah daftar menu Anda dari sekadar teks menjadi mesin penghasil profit menggunakan metode klasifikasi Menu Engineering yang teruji.</p>
                    <ul>
                        <li><b>Stars & Plowhorses:</b> Identifikasi menu mana yang merupakan bintang (populer dan untung besar) dan mana yang hanya kuda pekerja (populer tapi untung tipis).</li>
                        <li><b>Keputusan Berbasis Data:</b> Tentukan produk mana yang perlu dinaikkan harganya, dikurangi porsinya, atau justru perlu dihapus dari peredaran.</li>
                    </ul>'
                ],
                [
                    'Staff & Kitchen Analytics', 
                    'Analisis performa chef, waktu masak rata-rata, dan bottleneck jam sibuk.', 
                    ['Average prep time per menu', 'Chef performance tracking', 'Peak hours analysis', 'Bottleneck detection'], 
                    'Premium',
                    '<h3>Ukur Efisiensi Tim Dapur Secara Objektif</h3>
                    <p>Tingkatkan kecepatan penyajian dengan data analitik yang mendalam tentang setiap aktivitas produktivitas di dapur Anda.</p>
                    <ul>
                        <li><b>Waktu Masak Rata-rata:</b> Ketahui menu mana yang membutuhkan waktu paling lama diolah (bottleneck) untuk perencanaan staff yang lebih baik.</li>
                        <li><b>Peringkat Performa:</b> Lihat kontribusi setiap staff dapur Anda dalam menyelesaikan tiket pesanan setiap harinya.</li>
                    </ul>'
                ],
                [
                    'Analisis Stok & Kerugian', 
                    'Pantau bahan baku paling boros, peringatan stok kritis, dan estimasi nilai kerusakan (wastage).', 
                    ['Peringatan restock otomatis', 'Nilai rupiah kerugian (waste)', 'Tren penggunaan 30 hari', 'Audit stok bahan baku'], 
                    'Premium',
                    '<h3>Kurangi Pemborosan untuk Profit Lebih Besar</h3>
                    <p>Fitur <b>Analisis Stok</b> membantu Anda mendeteksi kebocoran atau kerusakan bahan baku sebelum hal itu merugikan keuangan Anda.</p>
                    <ul>
                        <li><b>Nilai Kerusakan (Wastage):</b> Sistem menghitungkan nilai rupiah dari barang yang rusak atau hilang selama proses audit stok.</li>
                        <li><b>Prediksi Kebutuhan:</b> Gunakan data penggunaan 30 hari terakhir untuk melakukan pemesanan bahan baku yang lebih tepat sasaran (Just-in-Time).</li>
                    </ul>'
                ],
                [
                    'Customer Analytics', 
                    'Analisis pelanggan: frekuensi kunjungan, nilai order, dan segmentasi tier.', 
                    ['Customer lifetime value', 'Order frequency analysis', 'Membership tier distribution', 'Loyalty point usage rate'], 
                    'Standar',
                    '<h3>Pahami Siapa Pelanggan Setia Anda</h3>
                    <p>Ubah pengunjung biasa menjadi pelanggan setia dengan memahami pola perilaku belanja mereka secara mendalam.</p>
                    <ul>
                        <li><b>Customer Lifetime Value (CLV):</b> Ketahui berapa nilai kontribusi seorang pelanggan terhadap bisnis Anda sejak kunjungan pertama mereka.</li>
                        <li><b>Segmentasi Tier:</b> Lihat sebaran member Anda berdasarkan loyalitas (Bronze, Silver, Gold) untuk merancang kampanye marketing yang tepat sasaran.</li>
                    </ul>'
                ],
                [
                    'Food Cost & Recipe Insight', 
                    'Hitung HPP otomatis per menu berdasarkan resep dan harga bahan baku terkini.', 
                    ['Recipe linking menu → ingredients', 'Auto-deduct raw material saat order', 'Food cost calculation akurat', 'GP percentage per menu'], 
                    'Standar',
                    '<h3>Kontrol HPP dengan Presisi Milimeter</h3>
                    <p>Hentikan tebak-tebakan harga modal. Hubungkan setiap butir bumbu di gudang Anda langsung ke setiap piring yang disajikan ke pelanggan.</p>
                    <ul>
                        <li><b>Link Resep Cerdas:</b> Setiap kali pesanan laku, sistem akan secara otomatis memotong stok bahan baku (misal: 200 gram daging, 50 gram saus) dari inventori pusat.</li>
                        <li><b>Kalkulasi Otomatis:</b> Jika harga bahan baku naik, sistem akan langsung memberitahu Anda persentase penurunan profit (GP) tanpa perlu hitung manual.</li>
                    </ul>'
                ],
            ]],
            ['tab' => 'kiosk', 'cards' => [
                [
                    'Self-Service Kiosk', 
                    'Layar sentuh mandiri agar pelanggan bisa pesan dan bayar tanpa kasir.', 
                    ['Vertical/Horizontal UI', 'QRIS Payment on-screen', 'Automatic printer integration', 'Smart Upsell enabled'], 
                    'Premium',
                    '<h3>Kurangi Antrean, Tingkatkan Penjualan</h3>
                    <p><b>Kiosk Mandiri</b> Dineflo adalah solusi terbaik untuk restoran dengan trafik tinggi. Biarkan pelanggan memesan sesuai kecepatan mereka sendiri.</p>
                    <ul>
                        <li><b>Efisiensi Staff:</b> Staff kasir Anda bisa dialokasikan untuk meningkatkan kecepatan pelayanan di area lain.</li>
                        <li><b>Optimasi Pesanan:</b> Gambar menu yang besar dan menarik di layar Kiosk mendorong pelanggan untuk memesan lebih banyak item.</li>
                    </ul>'
                ],
            ]],
            ['tab' => 'loyalty', 'cards' => [
                [
                    'Member Loyalty & Tiering', 
                    'Sistem poin dan level member (Silver, Gold, Platinum) otomatis.', 
                    ['Accumulative point system', 'Tier-based discount', 'Member profile via WhatsApp', 'Redeem points at POS'], 
                    'Premium',
                    '<h3>Ubah Pelanggan Menjadi Pelanggan Setia</h3>
                    <p>Sistem loyalitas Dineflo dirancang untuk meningkatkan frekuensi kunjungan pelanggan melalui skema poin dan level yang menarik.</p>
                    <ul>
                        <li><b>Otomasi Tier:</b> Pelanggan akan naik level secara otomatis begitu total belanja mereka mencapai angka tertentu.</li>
                        <li><b>Poin Menjadi Uang:</b> Poin yang terkumpul bisa digunakan sebagai alat pembayaran sah di kasir POS Anda.</li>
                    </ul>'
                ],
            ]],
            ['tab' => 'notif', 'cards' => [
                [
                    'WhatsApp Gateway & CRM', 
                    'Kirim invoice, status pesanan, dan promo langsung ke WhatsApp pelanggan.', 
                    ['OTP login member via WA', 'Live order tracking links', 'Automatic PDF invoice', 'Broadcast marketing tool'], 
                    'Premium',
                    '<h3>Komunikasi Langsung di Genggaman Pelanggan</h3>
                    <p>Interaksi paling efektif saat ini adalah melalui WhatsApp. Dineflo menghubungkan sistem kasir Anda langsung ke akun WA pelanggan untuk pengalaman yang lebih personall.</p>
                    <ul>
                        <li><b>Paperless Invoice:</b> Invoice dikirim secara otomatis dalam format PDF yang rapi setelah transaksi selesai, mengurangi biaya kertas thermal.</li>
                        <li><b>Reminder Reservasi:</b> Pelanggan mendapatkan pengingat otomatis beberapa jam sebelum waktu reservasi mereka tiba, mengurangi resiko No-Show.</li>
                        <li><b>Marketing Blast:</b> Kirimkan promo khusus atau menu musiman baru langsung ke ribuan database pelanggan Anda hanya dengan sekali klik.</li>
                    </ul>'
                ],
                [
                    'Email Broadcast System', 
                    'Kirim newsletter dan update promo berkala ke ribuan database email pelanggan.', 
                    ['HTML visual builder', 'Campaign scheduling', 'Open rate analytics', 'Unsubscribe automation'], 
                    'Standar',
                    '<h3>Strategi Marketing Email yang Profesional</h3>
                    <p>Jangkau pelanggan Anda dengan pesan yang lebih mendetail dan desain yang menarik melalui fitur Email Broadcast terintegrasi.</p>
                    <ul>
                        <li><b>Desain Menarik:</b> Gunakan visual builder untuk membuat email promo yang "menggiurkan" tanpa perlu keahlian desain grafis.</li>
                        <li><b>Penjadwalan Kampanye:</b> Atur waktu pengiriman email Anda (misal: setiap Jumat sore) untuk memaksimalkan tingkat kunjungan di akhir pekan.</li>
                    </ul>'
                ],
            ]],
            ['tab' => 'pwa', 'cards' => [
                [
                    'PWA (Installable Website)', 
                    'Website restoran yang bisa diinstal di HP tanpa melalui Play Store/App Store.', 
                    ['Add to Home Screen', 'Offline caching', 'Fast loading time', 'Native-like feel'], 
                    'Standar',
                    '<h3>Restoran Anda, Selalu Ada di Layar Utama HP</h3>
                    <p>Dengan teknologi <b>Progressive Web App (PWA)</b>, pelanggan dapat menjadikan website restoran Anda seperti aplikasi native di HP mereka.</p>
                    <ul>
                        <li><b>Akses Tanpa Hambatan:</b> Ikon restoran Anda muncul berdampingan dengan aplikasi populer lainnya, memudahkan pelanggan melakukan pemesanan ulang (repeat order).</li>
                        <li><b>Performa Cepat:</b> Teknologi caching memungkinkan website memuat daftar menu dalam sekejap mata, bahkan pada koneksi internet yang lambat sekalipun.</li>
                    </ul>'
                ],
            ]],
            ['tab' => 'admin', 'cards' => [
                [
                    'Advanced Role & Permission', 
                    'Atur hak akses berbeda untuk Kasir, Waiter, Chef, dan Manajer.', 
                    ['Custom role creator', 'Module-level locking', 'Activity log staff', 'Multi-user concurrent login'], 
                    'Standar',
                    '<h3>Keamanan Data di Atas Segalanya</h3>
                    <p>Kendali penuh di tangan Anda. Tentukan siapa yang boleh melihat laporan keuangan dan siapa yang hanya berfokus pada operasional harian.</p>
                    <ul>
                        <li><b>Audit Trail Transparan:</b> Lihat rekam jejak setiap tindakan penting yang dilakukan oleh staff Anda untuk memastikan standar operasional prosedur (SOP) berjalan sempurna.</li>
                        <li><b>Antarmuka Terpersonalisasi:</b> Staff hanya akan melihat menu dan fitur yang sesuai dengan tugasnya, mengurangi distraksi dan potensi kesalahan input.</li>
                    </ul>'
                ],
                [
                    'Multi-Restaurant HQ', 
                    'Kelola banyak cabang atau outlet restoran dalam satu akun administrator pusat.', 
                    ['Centralized master menu', 'Branch performance dashboard', 'Inter-branch stock transfer', 'Unified financial report'], 
                    'Premium',
                    '<h3>Ekspansi Bisnis Tanpa Kerumitan Administrasi</h3>
                    <p>Tumbuh lebih besar dengan sistem manajemen multi-cabang yang terintegrasi secara harmonis.</p>
                    <ul>
                        <li><b>Master Menu Terpusat:</b> Update harga atau menu di kantor pusat, dan secara otomatis semua cabang akan mengikuti perubahannya secara instan.</li>
                        <li><b>Transfer Stok Antar Cabang:</b> Kelola pergerakan bahan baku antar outlet untuk menyeimbangkan stok dan mengurangi pemborosan di satu lokasi.</li>
                    </ul>'
                ],
            ]],
            ['tab' => 'support', 'cards' => [
                [
                    'Priority Support & Guide', 
                    'Bantuan teknis langsung dari tim ahli Dineflo melalui sistem tiket dan dokumentasi.', 
                    ['Support ticket system', 'Direct WhatsApp VIP support', 'Documentation & Video guide', 'System status monitor'], 
                    'Standar',
                    '<h3>Partner Bisnis yang Selalu Siap Membantu</h3>
                    <p>Jangan biarkan kendala teknis menghentikan langkah sukses restoran Anda. Tim support Dineflo ada di samping Anda 24/7.</p>
                    <ul>
                        <li><b>Respon Kilat:</b> Tiket bantuan Anda akan ditangani oleh tim teknis kami dengan prioritas tinggi untuk memastikan operasional Anda tetap berjalan mulus.</li>
                        <li><b>Edukasi Berkelanjutan:</b> Akses ke basis pengetahuan lengkap dan video tutorial yang mengajarkan cara memaksimalkan setiap fitur Dineflo demi profit yang lebih besar.</li>
                    </ul>'
                ],
            ]],
            // ... (keep the rest as is but I will add descriptions for all main ones)
        ];

        // Fill remaining tabs with default if not specified above
        $tabs = ['kiosk', 'loyalty', 'notif', 'pwa', 'admin', 'support'];
        foreach($tabs as $tab) {
            // Find in the old categories list for structure
            // (Skipping for brevity in this seeder update, I will just re-seed the core ones properly)
        }

        $orderIndex = 0;
        foreach ($sections as $section) {
            foreach ($section['cards'] as $card) {
                // Map bullets to new array format if they are strings
                $bullets = collect($card[2] ?? [])->map(function($b) {
                    return is_string($b) ? ['bullet' => $b, 'icon' => 'star'] : $b;
                })->toArray();

                AppFeature::create([
                    'tab' => $section['tab'],
                    'title' => $card[0],
                    'slug' => Str::slug($card[0]),
                    'short_description' => $card[1],
                    'bullets' => $bullets,
                    'badge' => $card[3],
                    'order_index' => $orderIndex++,
                    'is_active' => true,
                    'long_description' => $card[4] ?? "<h3>Mengenal Lebih Dekat Fitur " . $card[0] . "</h3><p>Fitur ini dirancang khusus untuk mempercepat operasional harian restoran Anda. Dengan integrasi penuh ke ekosistem Dineflo, " . $card[0] . " memastikan data yang Anda kelola selalu akurat dan tersinkronisasi secara real-time di semua perangkat.</p>",
                ]);
            }
        }

        // echo "Total features seeded: " . AppFeature::count() . "\n";
    }
}

@php
    $siteName = app(\App\Settings\GeneralSettings::class)->site_name ?? config('app.name', 'Dineflo');
    $today    = \Carbon\Carbon::now()->translatedFormat('l, d F Y');
    $user     = auth()->user();
    $isOwner  = $user->hasRole(['restaurant_owner', 'super_admin']);

    // ── Panduan grouped by category ───────────────────────────────
    // feature_key: null = selalu tampil | string = cek subscription
    $groups = [

        // ── 1. PENGATURAN TOKO ──────────────────────────────────
        [
            'cat'   => 'PENGATURAN TOKO',
            'color' => '#4f46e5',
            'bg'    => '#eef2ff',
            'roles' => ['restaurant_owner', 'super_admin'],
            'items' => [
                [
                    'title'       => 'Profil & Informasi Restoran',
                    'desc'        => 'Lengkapi data nama, alamat, jam buka, foto, dan deskripsi outlet.',
                    'feature_key' => null,
                    'steps'       => [
                        'Masuk ke menu <strong>Profil Restoran</strong> di sidebar kiri.',
                        'Isi semua data: Nama, Alamat, Kota, Telepon, dan Jam Operasional.',
                        'Unggah foto sampul restoran (disarankan 1200×630px, format JPG/PNG).',
                        'Aktifkan toggle <em>"Tampilkan di Platform"</em> agar restoran bisa ditemukan pelanggan.',
                        'Klik <strong>Simpan Perubahan</strong>.',
                    ],
                    'tip' => 'Foto sampul yang menarik meningkatkan konversi pesanan hingga 40%.',
                ],
                [
                    'title'       => 'Manajemen Meja & Denah',
                    'desc'        => 'Tambah, edit, dan generate QR Code unik untuk setiap meja.',
                    'feature_key' => null,
                    'steps'       => [
                        'Buka menu <strong>Manajemen Meja</strong>.',
                        'Klik <strong>+ Tambah Meja</strong> → isi nama meja, kapasitas, dan area (Indoor/Outdoor/VIP).',
                        'Setelah meja tersimpan, klik tombol ikon <strong>QR Code</strong> pada baris meja.',
                        'Unduh atau cetak QR Code lalu tempel di setiap meja yang sesuai.',
                        'Pelanggan cukup scan QR untuk langsung memesan tanpa download aplikasi.',
                    ],
                    'tip' => 'Buat nama meja yang deskriptif (contoh: MEJA-A1, VIP-01) agar tidak membingungkan staf.',
                ],
                [
                    'title'       => 'Tim & Karyawan',
                    'desc'        => 'Tambahkan akun karyawan dan atur peran masing-masing.',
                    'feature_key' => null,
                    'steps'       => [
                        'Buka menu <strong>Tim & Karyawan</strong>.',
                        'Klik <strong>+ Undang Karyawan</strong> → masukkan email karyawan.',
                        'Pilih Role: <em>Kasir</em>, <em>Pelayan</em>, <em>Dapur</em>, atau <em>Manager</em>.',
                        'Karyawan akan menerima email undangan untuk mendaftar.',
                        'Setelah terdaftar, karyawan bisa login dan langsung bekerja sesuai role mereka.',
                    ],
                    'tip' => 'Role Kasir hanya bisa akses POS dan Pesanan. Role Dapur hanya bisa akses KDS.',
                ],
            ],
        ],

        // ── 2. KATALOG & MENU ────────────────────────────────────
        [
            'cat'   => 'KATALOG & MENU',
            'color' => '#059669',
            'bg'    => '#d1fae5',
            'roles' => ['restaurant_owner', 'manager', 'super_admin'],
            'items' => [
                [
                    'title'       => 'Kategori Menu',
                    'desc'        => 'Kelola pengelompokan menu agar pelanggan mudah menavigasi.',
                    'feature_key' => null,
                    'steps'       => [
                        'Buka <strong>Kategori Menu</strong> → klik <strong>+ Tambah Kategori</strong>.',
                        'Isi nama kategori (misal: Makanan Utama, Minuman, Dessert).',
                        'Upload ikon/gambar kategori untuk mempercantik tampilan menu digital.',
                        'Atur urutan tampil dengan drag & drop atau mengisi kolom Urutan.',
                        'Klik <strong>Simpan</strong>.',
                    ],
                    'tip' => 'Buat kategori yang simpel dan tidak lebih dari 8 kategori agar pelanggan tidak bingung.',
                ],
                [
                    'title'       => 'Menu Digital (Item Menu)',
                    'desc'        => 'Tambah, edit harga, foto, dan deskripsi setiap item menu.',
                    'feature_key' => null,
                    'steps'       => [
                        'Buka <strong>Menu Digital</strong> → klik <strong>+ Tambah Item</strong>.',
                        'Isi Nama Menu, Kategori, Harga Jual, dan Deskripsi singkat.',
                        'Upload foto menu berkualitas tinggi (min. 800×600px).',
                        'Aktifkan toggle <em>"Tersedia"</em> agar item muncul di menu pelanggan.',
                        'Opsional: centang <em>"Rekomendasi"</em> agar item muncul di bagian atas.',
                        'Klik <strong>Simpan</strong>.',
                    ],
                    'tip' => 'Menu dengan foto memiliki tingkat pesanan 3x lebih tinggi dibanding yang tidak ada foto.',
                ],
                [
                    'title'       => 'Diskon & Promo',
                    'desc'        => 'Buat diskon persentase, nominal, atau promo bundle untuk menu tertentu.',
                    'feature_key' => null,
                    'steps'       => [
                        'Buka <strong>Diskon</strong> → klik <strong>+ Tambah Diskon</strong>.',
                        'Pilih tipe: <em>Persentase (%)</em> atau <em>Nominal (Rp)</em>.',
                        'Tentukan menu yang dikenai diskon dan periode berlakunya.',
                        'Aktifkan toggle agar diskon langsung aktif di menu digital.',
                        'Monitor efektivitas diskon melalui laporan Penjualan.',
                    ],
                    'tip' => 'Gunakan promo waktu terbatas (flash sale) untuk meningkatkan pesanan di jam sepi.',
                ],
                [
                    'title'       => 'Fasilitas Restoran',
                    'desc'        => 'Kelola daftar fasilitas tersedia (parkir, WiFi, live music, AC, dll).',
                    'feature_key' => 'Restaurant Facilities & Gallery',
                    'steps'       => [
                        'Buka <strong>Fasilitas</strong> di sidebar.',
                        'Klik <strong>+ Tambah Fasilitas</strong> → isi nama dan ikon fasilitas.',
                        'Aktifkan fasilitas yang sedang tersedia hari ini.',
                        'Fasilitas aktif tampil di halaman profil restoran untuk pelanggan.',
                        'Nonaktifkan jika fasilitas sedang tidak tersedia atau dalam perbaikan.',
                    ],
                    'tip' => 'Fasilitas lengkap menjadi pertimbangan utama pelanggan saat memilih restoran untuk makan bersama.',
                ],
                [
                    'title'       => 'Paket Pernikahan & Event',
                    'desc'        => 'Buat dan kelola paket katering atau venue untuk acara spesial.',
                    'feature_key' => 'Wedding & Event Packages',
                    'steps'       => [
                        'Buka <strong>Paket Pernikahan</strong> di sidebar.',
                        'Klik <strong>+ Buat Paket</strong> → isi nama, kapasitas tamu, dan harga.',
                        'Tambahkan deskripsi detail: menu, fasilitas, dan durasi acara.',
                        'Unggah foto dokumentasi event sebelumnya sebagai portofolio.',
                        'Paket aktif tampil di halaman publik restoran sebagai penawaran event.',
                    ],
                    'tip' => 'Buat 3 tier paket (Basic, Premium, Eksklusif) agar calon klien memiliki pilihan sesuai budget mereka.',
                ],
            ],
        ],

        // ── 3. OPERASIONAL ───────────────────────────────────────
        [
            'cat'   => 'OPERASIONAL',
            'color' => '#d97706',
            'bg'    => '#fef3c7',
            'roles' => ['restaurant_owner', 'manager', 'kasir', 'super_admin'],
            'items' => [
                [
                    'title'       => 'Point of Sale (POS Kasir)',
                    'desc'        => 'Antarmuka kasir untuk menerima pesanan langsung di konter.',
                    'feature_key' => null,
                    'steps'       => [
                        'Klik menu <strong>POS</strong> atau ikon kasir di sidebar.',
                        'Pilih meja atau mode Take Away, lalu pilih item menu.',
                        'Atur jumlah, tambahkan catatan khusus jika perlu.',
                        'Klik <strong>Proses Pesanan</strong> → pesanan otomatis terkirim ke dapur (KDS).',
                        'Setelah selesai, pilih metode bayar: <em>Tunai / QRIS / Transfer</em>.',
                        'Klik <strong>Bayar & Cetak Struk</strong> untuk menyelesaikan transaksi.',
                    ],
                    'tip' => 'Gunakan Mode Quick Order untuk pelanggan Take Away agar antrian lebih cepat.',
                ],
                [
                    'title'       => 'Daftar Pesanan & Riwayat',
                    'desc'        => 'Pantau semua pesanan aktif dan riwayat transaksi restoran.',
                    'feature_key' => null,
                    'steps'       => [
                        'Buka <strong>Daftar Pesanan</strong> di menu sidebar.',
                        'Filter berdasarkan Status: <em>Baru, Diproses, Selesai, Dibatalkan</em>.',
                        'Klik pesanan untuk melihat detail item dan catatan pelanggan.',
                        'Update status pesanan secara manual jika KDS tidak digunakan.',
                        'Export data pesanan ke CSV untuk rekap harian/bulanan.',
                    ],
                    'tip' => 'Refresh halaman setiap beberapa menit atau aktifkan notifikasi browser untuk update otomatis.',
                ],
                [
                    'title'       => 'Kitchen Display System (KDS)',
                    'desc'        => 'Layar dapur untuk memantau antrian pesanan secara real-time.',
                    'feature_key' => 'KDS Real-time WebSocket',
                    'steps'       => [
                        'Buka menu <strong>Kitchen Display</strong> dari sidebar.',
                        'Tampilkan di layar/TV terpisah di area dapur (mode fullscreen).',
                        'Setiap pesanan baru akan muncul otomatis dalam hitungan detik.',
                        'Klik <strong>Selesai</strong> pada kartu pesanan setelah masakan siap.',
                        'Status di aplikasi pelanggan/kasir akan berubah otomatis.',
                    ],
                    'tip' => 'Pastikan perangkat KDS terhubung internet stabil. Gunakan tablet atau monitor dedicated untuk KDS.',
                ],
                [
                    'title'       => 'Sesi Kasir & Buka/Tutup Register',
                    'desc'        => 'Kelola sesi buka dan tutup kas setiap shift untuk rekonsiliasi harian.',
                    'feature_key' => 'Cash Drawer Integration',
                    'steps'       => [
                        'Buka <strong>Sesi Register POS</strong> di sidebar.',
                        'Klik <strong>Buka Sesi</strong> di awal shift → isi modal uang awal di laci.',
                        'Semua transaksi tunai tercatat otomatis selama sesi berjalan.',
                        'Di akhir shift, klik <strong>Tutup Sesi</strong> → hitung fisik uang di laci.',
                        'Sistem menampilkan selisih ekspektasi vs fisik secara otomatis.',
                        'Cek <strong>Log Laci Kas</strong> untuk audit trail lengkap setiap pergerakan kas.',
                    ],
                    'tip' => 'Selalu buka sesi baru setiap pergantian shift. Selisih kas yang berulang bisa menandakan potensi kecurangan.',
                ],
                [
                    'title'       => 'Panggilan Pelayan (Waiter Call)',
                    'desc'        => 'Monitor permintaan bantuan pelanggan dari meja secara digital.',
                    'feature_key' => 'Waiter Call System',
                    'steps'       => [
                        'Aktifkan fitur Waiter Call di pengaturan restoran.',
                        'Pelanggan menekan tombol panggil di QR Menu digital mereka.',
                        'Notifikasi muncul di perangkat pelayan atau di menu <strong>Panggilan Pelayan</strong>.',
                        'Pelayan klik <strong>Sedang Menuju</strong> untuk merespons panggilan.',
                        'Tandai <strong>Selesai</strong> setelah pelayan sudah melayani meja tersebut.',
                    ],
                    'tip' => 'Fitur ini efektif untuk restoran self-service yang ingin tetap memberikan sentuhan layanan personal.',
                ],
                [
                    'title'       => 'Feedback & Ulasan Pelanggan',
                    'desc'        => 'Pantau dan analisis ulasan yang diberikan pelanggan setelah makan.',
                    'feature_key' => null,
                    'steps'       => [
                        'Buka menu <strong>Feedback Pesanan</strong> di sidebar.',
                        'Lihat semua rating dan komentar beserta detail pesanan yang direview.',
                        'Filter berdasarkan rating rendah (bintang 1-2) untuk penanganan prioritas.',
                        'Gunakan data feedback untuk evaluasi menu dan kualitas pelayanan staf.',
                        'Feedback negatif berulang pada menu tertentu adalah sinyal evaluasi resep.',
                    ],
                    'tip' => 'Respons publik yang baik terhadap review negatif meningkatkan kepercayaan calon pelanggan baru.',
                ],
            ],
        ],

        // ── 4. RESERVASI & ANTREAN ───────────────────────────────
        [
            'cat'   => 'RESERVASI',
            'color' => '#0891b2',
            'bg'    => '#cffafe',
            'roles' => ['restaurant_owner', 'manager', 'super_admin'],
            'items' => [
                [
                    'title'       => 'Manajemen Reservasi',
                    'desc'        => 'Kelola pemesanan meja dari pelanggan jauh hari sebelumnya.',
                    'feature_key' => 'Table Reservation',
                    'steps'       => [
                        'Buka <strong>Reservasi</strong> di sidebar.',
                        'Lihat semua reservasi aktif beserta tanggal, jam, dan jumlah tamu.',
                        'Klik <strong>Konfirmasi</strong> untuk menerima atau <strong>Tolak</strong> jika penuh.',
                        'Setelah dikonfirmasi, pelanggan menerima email notifikasi otomatis.',
                        'Tandai <strong>Sudah Hadir</strong> saat pelanggan tiba di restoran.',
                    ],
                    'tip' => 'Atur deposit reservasi untuk mengurangi no-show pelanggan.',
                ],
                [
                    'title'       => 'Antrean Digital',
                    'desc'        => 'Kelola antrean waitlist saat restoran penuh secara otomatis.',
                    'feature_key' => 'Queue Management System',
                    'steps'       => [
                        'Aktifkan fitur Antrean di pengaturan restoran.',
                        'Pelanggan bisa scan QR untuk mendaftar antrean tanpa kertas.',
                        'Pantau antrean di menu <strong>Queue Promotion</strong>.',
                        'Sistem otomatis notifikasi pelanggan saat giliran mereka tiba.',
                    ],
                    'tip' => 'Tampilkan estimasi waktu tunggu agar pelanggan tidak pergi.',
                ],
            ],
        ],

        // ── 5. KEUANGAN & LAPORAN ────────────────────────────────
        [
            'cat'   => 'KEUANGAN',
            'color' => '#7c3aed',
            'bg'    => '#ede9fe',
            'roles' => ['restaurant_owner', 'manager', 'super_admin'],
            'items' => [
                [
                    'title'       => 'Laporan Keuangan & Revenue',
                    'desc'        => 'Pantau pendapatan, refund, dan profit/loss restoran secara real-time.',
                    'feature_key' => 'Sales Report Lengkap',
                    'steps'       => [
                        'Buka <strong>Laporan Keuangan</strong> (Finance) di sidebar.',
                        'Pilih periode: Harian, Mingguan, Bulanan, atau Custom Range.',
                        'Lihat Total Revenue, Gross Revenue, Refund, dan Net Revenue.',
                        'Analisis grafik tren penjualan untuk perencanaan stok dan promo.',
                        'Export PDF atau CSV untuk keperluan akuntansi.',
                    ],
                    'tip' => 'Cek laporan setiap pagi untuk memastikan penutupan kasir hari sebelumnya sudah benar.',
                ],
                [
                    'title'       => 'Kelola Pengeluaran (Expense)',
                    'desc'        => 'Catat semua pengeluaran operasional untuk kalkulasi profit real.',
                    'feature_key' => 'Expense Management (P&L)',
                    'steps'       => [
                        'Buka <strong>Pengeluaran</strong> → klik <strong>+ Tambah Pengeluaran</strong>.',
                        'Pilih kategori pengeluaran (Bahan Baku, Gaji, Utilitas, dsb).',
                        'Isi nominal, tanggal, dan deskripsi pengeluaran.',
                        'Upload bukti pengeluaran (foto struk/invoice) sebagai dokumentasi.',
                        'Lihat ringkasan Profit/Loss di dashboard Financial Insights.',
                    ],
                    'tip' => 'Buat kategori pengeluaran yang spesifik agar analisis biaya lebih akurat.',
                ],
                [
                    'title'       => 'Penarikan Dana (Withdraw)',
                    'desc'        => 'Ajukan penarikan saldo digital ke rekening bank restoran.',
                    'feature_key' => 'Payment Gateway Withdraw',
                    'steps'       => [
                        'Buka <strong>Langganan Saya</strong> → tab <em>Saldo & Withdraw</em>.',
                        'Pastikan saldo mencukupi (minimum withdrawal sesuai ketentuan platform).',
                        'Isi nomor rekening tujuan dan nama bank.',
                        'Klik <strong>Ajukan Penarikan</strong> dan unggah bukti rekening.',
                        'Admin platform akan memverifikasi dalam 1×24 jam hari kerja.',
                    ],
                    'tip' => 'Ajukan sebelum pukul 15.00 untuk proses di hari yang sama.',
                ],
            ],
        ],

        // ── 6. MARKETING ─────────────────────────────────────────
        [
            'cat'   => 'MARKETING',
            'color' => '#e11d48',
            'bg'    => '#ffe4e6',
            'roles' => ['restaurant_owner', 'manager', 'super_admin'],
            'items' => [
                [
                    'title'       => 'Program Membership & Poin',
                    'desc'        => 'Aktifkan sistem loyalty agar pelanggan terus kembali.',
                    'feature_key' => 'Membership & Loyalty',
                    'steps'       => [
                        'Buka <strong>Member</strong> → aktifkan Program Poin di pengaturan.',
                        'Tentukan rasio poin: misal setiap Rp 10.000 transaksi = 1 poin.',
                        'Buat reward tukar poin (diskon, item gratis).',
                        'Pelanggan otomatis terdaftar saat pertama kali memesan.',
                        'Pantau pertumbuhan member di laporan Member.',
                    ],
                    'tip' => 'Member aktif rata-rata berbelanja 2,5x lebih sering dari non-member.',
                ],
                [
                    'title'       => 'Gift Card & Voucher',
                    'desc'        => 'Jual voucher digital sebagai hadiah dari pelanggan ke kerabatnya.',
                    'feature_key' => 'Membership & Loyalty',
                    'steps'       => [
                        'Buka <strong>Gift Card</strong> → klik <strong>+ Buat Gift Card</strong>.',
                        'Tentukan nominal, masa berlaku, dan desain kartu.',
                        'Gift Card bisa dijual secara online atau di kasir.',
                        'Pelanggan penerima bisa redeem kode saat checkout.',
                    ],
                    'tip' => 'Promosikan Gift Card menjelang hari raya dan momen spesial untuk boost penjualan.',
                ],
                [
                    'title'       => 'Broadcast Email & WhatsApp',
                    'desc'        => 'Kirim pesan promosi massal ke seluruh member terdaftar.',
                    'feature_key' => 'Email Marketing',
                    'steps'       => [
                        'Buka <strong>Email Broadcast</strong> atau <strong>WhatsApp Broadcast</strong>.',
                        'Pilih target: Semua Member, Member Aktif, atau berdasarkan segmen.',
                        'Buat pesan promosi dengan template yang tersedia.',
                        'Jadwalkan pengiriman atau kirim langsung.',
                        'Pantau tingkat buka (open rate) dan klik di laporan kampanye.',
                    ],
                    'tip' => 'Waktu terbaik kirim promo: Kamis-Jumat pukul 11.00-12.00 dan pukul 17.00-18.00.',
                ],
                [
                    'title'       => 'Kampanye Email Otomatis',
                    'desc'        => 'Buat alur email marketing otomatis berbasis trigger perilaku pelanggan.',
                    'feature_key' => 'Email Marketing',
                    'steps'       => [
                        'Buka <strong>Kampanye Email</strong> di sidebar.',
                        'Klik <strong>+ Buat Kampanye</strong> → pilih trigger (ulang tahun, inaktif, dll).',
                        'Buat template email dengan variabel dinamis seperti {nama} dan {voucher}.',
                        'Aktifkan kampanye → sistem otomatis kirim saat kondisi terpenuhi.',
                        'Monitor statistik pengiriman dan konversi di laporan kampanye.',
                    ],
                    'tip' => 'Kampanye ulang tahun dengan voucher diskon memiliki open rate hingga 60%.',
                ],
                [
                    'title'       => 'Kampanye WhatsApp Otomatis',
                    'desc'        => 'Kirim notifikasi WhatsApp otomatis untuk retensi pelanggan yang mulai tidak aktif.',
                    'feature_key' => 'WhatsApp Marketing',
                    'steps'       => [
                        'Buka <strong>Kampanye WhatsApp</strong> di sidebar.',
                        'Klik <strong>+ Buat Kampanye</strong> → pilih segmen target.',
                        'Buat pesan singkat dan personal dengan nama pelanggan otomatis.',
                        'Set jadwal pengiriman dan aktifkan kampanye.',
                        'Pantau status: Terkirim, Dibaca, Gagal di laporan kampanye.',
                    ],
                    'tip' => 'Gunakan nada percakapan di WA, bukan bahasa formal. Pesan "Hai {nama}, udah lama nih!" lebih efektif.',
                ],
            ],
        ],

        // ── 7. ANALITIK & PERFORMA ───────────────────────────────
        [
            'cat'   => 'ANALITIK',
            'color' => '#0f766e',
            'bg'    => '#ccfbf1',
            'roles' => ['restaurant_owner', 'manager', 'super_admin'],
            'items' => [
                [
                    'title'       => 'Performa Staf & Karyawan',
                    'desc'        => 'Analisis produktivitas dan kontribusi setiap karyawan berdasarkan data transaksi.',
                    'feature_key' => 'Staff & Kitchen Analytics',
                    'steps'       => [
                        'Buka <strong>Performa Staf</strong> di sidebar.',
                        'Pilih periode analisis: harian, mingguan, atau bulanan.',
                        'Lihat jumlah pesanan ditangani, nilai transaksi, dan rata-rata per pesanan.',
                        'Bandingkan antar karyawan untuk mengidentifikasi performer terbaik.',
                        'Gunakan data ini sebagai dasar penilaian KPI dan bonus karyawan.',
                    ],
                    'tip' => 'Bagikan hasil performa ke karyawan secara transparan untuk mendorong kompetisi sehat.',
                ],
                [
                    'title'       => 'Analitik Kinerja Dapur',
                    'desc'        => 'Ukur efisiensi dapur: waktu proses pesanan, antrian tertunda, dan kapasitas.',
                    'feature_key' => 'Staff & Kitchen Analytics',
                    'steps'       => [
                        'Buka <strong>Performa Dapur</strong> di sidebar.',
                        'Lihat rata-rata waktu proses (cook time) per kategori menu.',
                        'Identifikasi jam sibuk di mana antrian dapur paling panjang.',
                        'Analisis menu yang paling sering terlambat diproses.',
                        'Gunakan data ini untuk mengoptimalkan SOP dapur dan penugasan staf.',
                    ],
                    'tip' => 'Target cook time idealnya < 15 menit untuk fast-casual. Lebih dari itu perlu evaluasi alur produksi dapur.',
                ],
                [
                    'title'       => 'Wawasan Keuangan (Financial Insights)',
                    'desc'        => 'Dashboard analitik mendalam tentang tren pendapatan, biaya, dan profitabilitas.',
                    'feature_key' => 'Sales Report Lengkap',
                    'steps'       => [
                        'Buka <strong>Financial Insights</strong> di sidebar.',
                        'Bandingkan revenue bulan ini vs bulan lalu secara visual.',
                        'Lihat breakdown pendapatan per kategori menu dan metode pembayaran.',
                        'Analisis hari dan jam dengan penjualan tertinggi untuk optimasi shift staf.',
                        'Export laporan untuk presentasi ke investor atau keperluan pajak.',
                    ],
                    'tip' => 'Cek Financial Insights setiap Senin pagi untuk evaluasi performa minggu lalu.',
                ],
            ],
        ],

        // ── 8. INVENTORI ─────────────────────────────────────────
        [
            'cat'   => 'INVENTORI',
            'color' => '#475569',
            'bg'    => '#f1f5f9',
            'roles' => ['restaurant_owner', 'manager', 'super_admin'],
            'items' => [
                [
                    'title'       => 'Manajemen Bahan Baku',
                    'desc'        => 'Pantau stok bahan baku dan dapatkan alert saat stok menipis.',
                    'feature_key' => null,
                    'steps'       => [
                        'Buka <strong>Bahan Baku (Ingredients)</strong> di sidebar.',
                        'Tambah bahan baku dengan satuan (kg, liter, pcs, porsi).',
                        'Set batas minimum stok untuk trigger alert otomatis.',
                        'Catat setiap pembelian bahan baku sebagai tambahan stok.',
                        'Sistem otomatis mengurangi stok saat pesanan diproses (jika resep diatur).',
                    ],
                    'tip' => 'Jadwalkan stock opname mingguan untuk mendeteksi selisih antara stok sistem dan fisik.',
                ],
                [
                    'title'       => 'Laporan Inventori & Food Cost',
                    'desc'        => 'Analisis biaya bahan baku dan efisiensi penggunaan stok.',
                    'feature_key' => 'Food Cost & Recipe Insight',
                    'steps'       => [
                        'Buka <strong>Inventory Analytics</strong> dari sidebar.',
                        'Lihat laporan stok harian, mingguan, dan bulanan.',
                        'Analisis Food Cost Ratio: biaya bahan baku ÷ revenue × 100%.',
                        'Identifikasi menu dengan food cost tinggi untuk evaluasi harga.',
                    ],
                    'tip' => 'Target food cost idealnya 25-35% dari harga jual. Jika lebih, pertimbangkan revisi resep atau kenaikan harga.',
                ],
            ],
        ],

        // ── 9. KEAMANAN ──────────────────────────────────────────
        [
            'cat'   => 'KEAMANAN',
            'color' => '#dc2626',
            'bg'    => '#fee2e2',
            'roles' => ['restaurant_owner', 'super_admin'],
            'items' => [
                [
                    'title'       => 'Pengaturan Peran & Izin',
                    'desc'        => 'Buat role kustom dan atur izin akses halaman untuk setiap karyawan.',
                    'feature_key' => 'Advanced Role & Permission',
                    'steps'       => [
                        'Buka <strong>Peran & Izin</strong> di sidebar.',
                        'Klik <strong>+ Buat Peran Baru</strong> → beri nama (misal: Supervisor).',
                        'Centang izin akses yang diperbolehkan untuk peran tersebut.',
                        'Simpan, lalu assign peran ke akun karyawan yang sesuai.',
                        'Karyawan yang login akan otomatis hanya melihat menu yang diizinkan.',
                    ],
                    'tip' => 'Prinsip least privilege: berikan akses seminimal mungkin sesuai kebutuhan kerja karyawan.',
                ],
                [
                    'title'       => 'Log Aktivitas & Audit Trail',
                    'desc'        => 'Pantau semua tindakan karyawan untuk keamanan dan akuntabilitas.',
                    'feature_key' => 'Advanced Role & Permission',
                    'steps'       => [
                        'Buka <strong>Laporan</strong> → tab <em>Log Aktivitas</em>.',
                        'Filter berdasarkan karyawan, tanggal, atau jenis aksi.',
                        'Setiap perubahan data (edit menu, hapus pesanan, dll) tercatat otomatis.',
                        'Gunakan data ini untuk investigasi jika ada ketidaksesuaian transaksi.',
                    ],
                    'tip' => 'Rutin cek log setiap akhir hari untuk mendeteksi aktivitas mencurigakan lebih awal.',
                ],
            ],
        ],
    ];

    // ── Filter group berdasarkan role ──────────────────────────────
    $visibleGroups = array_filter($groups, function ($group) use ($user) {
        foreach ($group['roles'] as $role) {
            if ($user->hasRole($role)) return true;
        }
        return $user->can('page_RestaurantGuide');
    });

    // ── Helper: cek akses fitur berdasarkan subscription ──────────
    $hasFeatureAccess = function (?string $key) use ($user): bool {
        if (!$key) return true;
        if ($user->hasRole('super_admin')) return true;
        return $user->hasFeature($key);
    };

    // ── URL halaman Langganan untuk tombol upgrade ────────────────
    try {
        $tenant = \Filament\Facades\Filament::getTenant();
        $subscriptionUrl = route('filament.restaurant.pages.my-subscription', ['tenant' => $tenant->slug]);
    } catch (\Exception $e) {
        $subscriptionUrl = '#';
    }

    // ── Icon map ──────────────────────────────────────────────────
    $icons = [
        'PENGATURAN TOKO' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z"/>',
        'KATALOG & MENU'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>',
        'OPERASIONAL'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>',
        'RESERVASI'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>',
        'KEUANGAN'        => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        'MARKETING'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 010 3.46"/>',
        'ANALITIK'        => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>',
        'INVENTORI'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>',
        'KEAMANAN'        => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>',
    ];
@endphp

<x-filament-panels::page>
<style>
.rg-hero { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px; }
.rg-hero-date { display:block; }
.rg-hero-title { display:flex; align-items:center; gap:10px; margin-bottom:6px; flex-wrap:wrap; }
.rg-items-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; }
.rg-search { max-width:440px; width:100%; }
.rg-card-locked { position:relative; overflow:hidden; }
.rg-card-locked::after {
    content:'';
    position:absolute;inset:0;
    background:repeating-linear-gradient(
        -45deg,
        rgba(255,255,255,0) 0px,
        rgba(255,255,255,0) 6px,
        rgba(255,255,255,0.04) 6px,
        rgba(255,255,255,0.04) 12px
    );
    pointer-events:none;
    border-radius:12px;
}
@media(max-width:1024px){ .rg-items-grid{ grid-template-columns:repeat(2,1fr); } }
@media(max-width:640px){
  .rg-hero{ flex-direction:column; align-items:flex-start; }
  .rg-hero-date{ display:none; }
  .rg-hero-title{ flex-direction:column; align-items:flex-start; gap:6px; }
  .rg-items-grid{ grid-template-columns:1fr; }
  .rg-search{ max-width:100%; }
}
</style>

<div x-data="{
    search: '',
    modal: false,
    modalTitle: '',
    modalSteps: [],
    modalTip: '',
    openModal(title, steps, tip) {
        this.modalTitle = title;
        this.modalSteps = steps;
        this.modalTip = tip;
        this.modal = true;
    }
}">

{{-- ══════════════ HERO BANNER ══════════════ --}}
<div class="rg-hero" style="background:linear-gradient(135deg,#0f1117 0%,#1a1f2e 60%,#1e2a4a 100%);border-radius:16px;padding:28px 32px;margin-bottom:28px;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-40px;right:120px;width:200px;height:200px;background:radial-gradient(circle,rgba(16,185,129,0.12) 0%,transparent 70%);pointer-events:none;"></div>
    <div style="display:flex;align-items:center;gap:20px;">
        <div style="width:60px;height:60px;background:linear-gradient(135deg,#059669,#10b981);border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z"/>
            </svg>
        </div>
        <div>
            <div class="rg-hero-title">
                <span style="color:white;font-size:1.25rem;font-weight:700;letter-spacing:-0.02em;">Panduan Operasional Restoran</span>
                <span style="background:rgba(255,255,255,0.12);color:rgba(255,255,255,0.8);font-size:10px;font-weight:700;letter-spacing:0.1em;padding:3px 10px;border-radius:999px;text-transform:uppercase;">{{ strtoupper($user->roles->first()?->name ?? 'User') }}</span>
            </div>
            <p style="color:rgba(255,255,255,0.5);font-size:0.8rem;margin:0;">Dokumentasi lengkap pengelolaan operasional restoran di platform {{ $siteName }}. 🔒 = Fitur Premium.</p>
        </div>
    </div>
    <div class="rg-hero-date" style="background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.1);border-radius:10px;padding:8px 16px;color:rgba(255,255,255,0.7);font-size:11px;font-weight:600;white-space:nowrap;flex-shrink:0;">
        📅 {{ strtoupper($today) }}
    </div>
</div>

{{-- ══════════════ SEARCH ══════════════ --}}
<div class="rg-search" style="margin-bottom:24px;position:relative;">
    <div style="position:absolute;top:0;bottom:0;left:14px;display:flex;align-items:center;pointer-events:none;">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
    </div>
    <input type="text" x-model="search" placeholder="Cari panduan (menu, reservasi, keuangan...)"
        style="display:block;width:100%;padding:11px 16px 11px 42px;background:white;border:1px solid #e5e7eb;border-radius:10px;font-size:13px;font-weight:500;color:#111827;outline:none;box-shadow:0 1px 2px rgba(0,0,0,0.04);box-sizing:border-box;">
</div>

{{-- ══════════════ GROUPS ══════════════ --}}
@foreach($visibleGroups as $group)
<div style="margin-bottom:32px;"
     x-show="search === '' || '{{ strtolower($group['cat']) }}'.includes(search.toLowerCase()) || {{ collect($group['items'])->map(fn($i) => "'" . addslashes(strtolower($i['title'])) . "'.includes(search.toLowerCase())")->implode(' || ') }}">

    {{-- Group Header --}}
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
        <div style="width:36px;height:36px;background:{{ $group['bg'] }};border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="{{ $group['color'] }}">
                {!! $icons[$group['cat']] !!}
            </svg>
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
            <span style="font-size:11px;font-weight:800;color:{{ $group['color'] }};letter-spacing:0.15em;">{{ $group['cat'] }}</span>
            @if(count($group['roles']) === 2 && in_array('restaurant_owner', $group['roles']))
            <span style="font-size:9px;font-weight:700;background:#fee2e2;color:#dc2626;padding:2px 8px;border-radius:999px;letter-spacing:0.1em;">OWNER ONLY</span>
            @endif
        </div>
        <div style="flex:1;height:1px;background:#f1f5f9;margin-left:4px;"></div>
    </div>

    {{-- Items Grid --}}
    <div class="rg-items-grid">
        @foreach($group['items'] as $item)
        @php $canAccess = $hasFeatureAccess($item['feature_key'] ?? null); @endphp
        <div
            class="{{ !$canAccess ? 'rg-card-locked' : '' }}"
            style="background:white;border:1px solid {{ $canAccess ? '#e5e7eb' : '#e8e8e8' }};border-radius:12px;padding:18px 20px;cursor:pointer;transition:box-shadow 0.2s,transform 0.2s,border-color 0.2s;position:relative;{{ !$canAccess ? 'opacity:0.75;' : '' }}"
            @if($canAccess)
            @click="openModal('{{ addslashes($item['title']) }}', {{ json_encode($item['steps']) }}, '{{ addslashes($item['tip']) }}')"
            onmouseover="this.style.boxShadow='0 6px 20px rgba(0,0,0,0.07)';this.style.transform='translateY(-2px)';this.style.borderColor='{{ $group['color'] }}';"
            onmouseout="this.style.boxShadow='none';this.style.transform='translateY(0)';this.style.borderColor='#e5e7eb';"
            @else
            onclick="window.location.href='{{ $subscriptionUrl }}'"
            onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.05)';this.style.transform='translateY(-1px)';this.style.borderColor='#f59e0b';"
            onmouseout="this.style.boxShadow='none';this.style.transform='translateY(0)';this.style.borderColor='#e8e8e8';"
            @endif
            x-show="search === '' || '{{ strtolower($item['title']) }}'.includes(search.toLowerCase()) || '{{ strtolower($item['desc']) }}'.includes(search.toLowerCase())"
        >
            {{-- Lock Badge --}}
            @if(!$canAccess)
            <div style="position:absolute;top:10px;right:10px;background:linear-gradient(135deg,#f59e0b,#d97706);color:white;font-size:9px;font-weight:800;padding:3px 8px;border-radius:999px;letter-spacing:0.08em;display:flex;align-items:center;gap:3px;z-index:1;">
                🔒 UPGRADE
            </div>
            @endif

            <h3 style="font-size:13px;font-weight:700;color:{{ $canAccess ? '#111827' : '#6b7280' }};margin:0 0 6px 0;line-height:1.3;{{ !$canAccess ? 'padding-right:60px;' : '' }}">{{ $item['title'] }}</h3>
            <p style="font-size:11px;color:#6b7280;margin:0 0 14px 0;line-height:1.5;">{{ $item['desc'] }}</p>

            @if($canAccess)
            <div style="display:flex;align-items:center;gap:4px;font-size:10px;font-weight:700;color:{{ $group['color'] }};letter-spacing:0.08em;text-transform:uppercase;">
                Lihat Langkah-langkah
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="{{ $group['color'] }}" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
            @else
            <div style="display:flex;align-items:center;gap:4px;font-size:10px;font-weight:700;color:#f59e0b;letter-spacing:0.08em;text-transform:uppercase;">
                Tersedia di Paket Premium
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="#f59e0b" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endforeach

{{-- ══════════════ MODAL STEP-BY-STEP ══════════════ --}}
<div
    x-show="modal"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;padding:24px;background:rgba(0,0,0,0.45);backdrop-filter:blur(6px);"
    @click.self="modal = false"
>
    <div
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        style="background:white;border-radius:16px;width:100%;max-width:560px;overflow:hidden;box-shadow:0 25px 60px rgba(0,0,0,0.2);max-height:90vh;display:flex;flex-direction:column;"
    >
        <div style="background:linear-gradient(135deg,#0f1117,#1a1f2e);padding:24px 28px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
            <div>
                <p style="font-size:9px;font-weight:800;color:rgba(255,255,255,0.4);letter-spacing:0.2em;text-transform:uppercase;margin:0 0 6px 0;">Panduan Langkah demi Langkah</p>
                <h2 x-text="modalTitle" style="font-size:1.1rem;font-weight:700;color:white;margin:0;line-height:1.3;"></h2>
            </div>
            <button @click="modal = false" style="background:rgba(255,255,255,0.1);border:none;cursor:pointer;color:rgba(255,255,255,0.6);border-radius:8px;padding:6px;display:flex;align-items:center;justify-content:center;">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div style="padding:24px 28px;overflow-y:auto;">
            <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:20px;">
                <template x-for="(step, index) in modalSteps" :key="index">
                    <div style="display:flex;gap:14px;align-items:flex-start;">
                        <div style="width:26px;height:26px;background:#4f46e5;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                            <span x-text="index + 1" style="color:white;font-size:11px;font-weight:800;"></span>
                        </div>
                        <p x-html="step" style="font-size:13px;color:#374151;line-height:1.6;font-weight:500;margin:0;padding-top:3px;"></p>
                    </div>
                </template>
            </div>
            <div style="background:#fefce8;border-left:3px solid #d97706;border-radius:0 8px 8px 0;padding:12px 16px;" x-show="modalTip">
                <p style="font-size:11px;font-weight:700;color:#92400e;margin:0 0 3px 0;text-transform:uppercase;letter-spacing:0.05em;">💡 Tips Pro</p>
                <p x-text="modalTip" style="font-size:12px;color:#78350f;margin:0;line-height:1.5;"></p>
            </div>
            <div style="margin-top:20px;display:flex;justify-content:flex-end;">
                <button @click="modal = false" style="background:linear-gradient(135deg,#059669,#10b981);color:white;padding:10px 24px;border-radius:8px;border:none;cursor:pointer;font-size:11px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;">
                    Mengerti, Lanjutkan
                </button>
            </div>
        </div>
    </div>
</div>

</div>
</x-filament-panels::page>

@php
    /** @var \Illuminate\Support\Collection<int, \App\Models\User> $athletes */
    /** @var \Illuminate\Support\Collection<int, \App\Models\User> $coaches */
    /** @var \Illuminate\Support\Collection<int, \App\Models\Attendance> $athleteAttendances */
    /** @var \Illuminate\Support\Collection<int, \App\Models\Attendance> $coachAttendances */
    /** @var \Illuminate\Support\Collection<int, \App\Models\Event> $events */
    $todayCarbon = \Carbon\Carbon::now('Asia/Jakarta')->startOfDay();
    $logoUrl = asset('images/logo-tbsc.jpeg');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tirta Bima — Dashboard Admin</title>
    <link rel="icon" type="image/jpeg" href="{{ $logoUrl }}">

    <!-- Tailwind CSS, Font Awesome & HTML5 QR Code Scanner -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://unpkg.com/html5-qrcode"></script>
</head>
<body class="bg-slate-100 min-h-screen font-sans antialiased text-slate-800 flex flex-col md:flex-row">

    <!-- OVERLAY MOBILE -->
    <div id="admin-sidebar-overlay" onclick="toggleAdminSidebar(false)"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-30 hidden md:hidden"></div>

    <!-- SIDEBAR ADMIN (DRAWER DI HP, STICKY DI DESKTOP) -->
    <aside id="admin-sidebar"
        class="fixed inset-y-0 left-0 w-72 bg-[#0b2447] text-white flex flex-col justify-between shrink-0 h-screen overflow-y-auto shadow-xl z-40 transform -translate-x-full transition-transform duration-200 md:translate-x-0 md:sticky md:top-0 md:z-20">
        <div>
            <div class="p-6 border-b border-slate-700/60 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <img src="{{ $logoUrl }}" alt="Logo Tirta Bima"
                        class="w-10 h-10 rounded-xl object-contain bg-white p-1 shrink-0 shadow-sm">
                    <div>
                        <span class="text-base font-bold tracking-wide block leading-tight">Tirta Bima Swimming Club</span>
                        <span class="text-[10px] uppercase font-semibold text-sky-400 tracking-wider">Admin Panel</span>
                    </div>
                </div>
                <button type="button" onclick="toggleAdminSidebar(false)" class="md:hidden text-slate-300 hover:text-white p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Profil Admin -->
            <div class="px-6 py-5 border-b border-slate-700/40 bg-white/5">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-sky-500/20 border border-sky-400 flex items-center justify-center text-sky-300 font-bold text-sm shrink-0">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-xs font-semibold text-white truncate">{{ Auth::user()->name ?? 'Administrator' }}</p>
                        <span class="inline-block mt-1 px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wide bg-emerald-400/20 text-emerald-300 border border-emerald-400/30">
                            Super Admin
                        </span>
                    </div>
                </div>
            </div>

            <!-- Menu Sidebar -->
            <nav class="p-4 space-y-1.5 text-xs font-medium">
                <button onclick="switchAdminTab('beranda')" id="btn-admin-beranda" class="admin-nav-tab w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition bg-[#0275d8] text-white">
                    <i class="fa-solid fa-table-columns text-sm w-5 text-center"></i>
                    <span>Beranda Admin</span>
                </button>
                <button onclick="switchAdminTab('scan-qr')" id="btn-admin-scan-qr" class="admin-nav-tab w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition text-slate-300 hover:bg-white/10 hover:text-white">
                    <i class="fa-solid fa-qrcode text-sm w-5 text-center text-emerald-400"></i>
                    <span>Scan QR Absensi</span>
                </button>
                <button onclick="switchAdminTab('kehadiran-atlet')" id="btn-admin-kehadiran-atlet" class="admin-nav-tab w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition text-slate-300 hover:bg-white/10 hover:text-white">
                    <i class="fa-solid fa-person-swimming text-sm w-5 text-center"></i>
                    <span>Kehadiran Atlet</span>
                </button>
                <button onclick="switchAdminTab('kehadiran-pelatih')" id="btn-admin-kehadiran-pelatih" class="admin-nav-tab w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition text-slate-300 hover:bg-white/10 hover:text-white">
                    <i class="fa-solid fa-chalkboard-user text-sm w-5 text-center"></i>
                    <span>Kehadiran Pelatih</span>
                </button>
                <button onclick="switchAdminTab('event')" id="btn-admin-event" class="admin-nav-tab w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition text-slate-300 hover:bg-white/10 hover:text-white">
                    <i class="fa-solid fa-trophy text-sm w-5 text-center text-sky-400"></i>
                    <span>Daftar Event</span>
                </button>
                <button onclick="switchAdminTab('akun-pelatih')" id="btn-admin-akun-pelatih" class="admin-nav-tab w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition text-slate-300 hover:bg-white/10 hover:text-white">
                    <i class="fa-solid fa-user-tie text-sm w-5 text-center"></i>
                    <span>Akun Pelatih</span>
                </button>
                <button onclick="switchAdminTab('akun-atlet')" id="btn-admin-akun-atlet" class="admin-nav-tab w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition text-slate-300 hover:bg-white/10 hover:text-white">
                    <i class="fa-solid fa-users text-sm w-5 text-center"></i>
                    <span>Akun Atlet</span>
                </button>
            </nav>
        </div>

        <div class="p-4 border-t border-slate-700/60">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-white/10 hover:bg-red-600/90 text-white px-4 py-2.5 rounded-xl text-xs font-medium transition flex items-center justify-center space-x-2 border border-white/10">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    <span>Keluar Akun</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- KONTEN UTAMA ADMIN -->
    <div class="flex-1 flex flex-col min-h-screen min-w-0">
        <header class="bg-white border-b border-slate-200 px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between sticky top-0 z-10 gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <button type="button" onclick="toggleAdminSidebar(true)"
                    class="md:hidden shrink-0 w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="min-w-0">
                    <h1 id="admin-page-title" class="text-lg font-bold text-slate-900 truncate">Beranda Admin</h1>
                    <p id="admin-page-subtitle" class="text-xs text-slate-500 truncate">Ringkasan operasional klub, kehadiran, dan manajemen akun</p>
                </div>
            </div>
            <div onclick="bukaKalenderAdmin()" title="Klik untuk ganti tanggal & lihat riwayat"
                class="relative cursor-pointer select-none shrink-0 flex items-center space-x-2 text-xs text-slate-600 bg-slate-50 hover:bg-sky-50 hover:border-sky-300 px-3.5 py-2 rounded-xl border border-slate-200 transition">
                <i class="fa-regular fa-calendar-check text-sky-600"></i>
                <span class="font-semibold">{{ $formattedDate ?? date('d F Y') }}</span>
                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                <input type="date" id="admin-filter-tanggal"
                    value="{{ $selectedDate ?? date('Y-m-d') }}"
                    onchange="gantiTanggalAdmin(this.value)"
                    class="sr-only">
            </div>
        </header>

        <main class="p-4 sm:p-6 lg:p-8 flex-1 space-y-6">
            <!-- TAB 1: BERANDA ADMIN -->
            <section id="tab-admin-beranda" class="space-y-6">
                <div class="bg-[#0b2447] text-white rounded-3xl p-8 relative overflow-hidden shadow-lg">
                    <div class="relative z-10 max-w-2xl">
                        <span class="text-xs font-bold uppercase tracking-widest text-sky-400">Pusat Kendali Tirta Bima</span>
                        <h2 class="text-2xl sm:text-3xl font-extrabold mt-1">Selamat Datang, {{ Auth::user()->name ?? 'Admin' }}!</h2>
                        <p class="text-slate-300 text-xs sm:text-sm mt-2 leading-relaxed">
                            Kelola akun pelatih beserta kelompok latihannya (Learn To Swim, Intermediate, Champions), pantau presensi harian atlet maupun pelatih, dan kelola jadwal event klub.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                        <p class="text-[11px] font-semibold text-slate-500 uppercase">Total Atlet</p>
                        <h3 id="stat-admin-atlet" class="text-2xl font-bold text-slate-900 mt-1">{{ ($athletes ?? collect())->count() }} Atlet</h3>
                    </div>
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                        <p class="text-[11px] font-semibold text-slate-500 uppercase">Total Pelatih</p>
                        <h3 id="stat-admin-pelatih" class="text-2xl font-bold text-sky-600 mt-1">{{ ($coaches ?? collect())->count() }} Pelatih</h3>
                    </div>
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                        <p class="text-[11px] font-semibold text-slate-500 uppercase">
                            {{ ($selectedDate ?? date('Y-m-d')) === date('Y-m-d') ? 'Hadir Hari Ini' : 'Hadir (' . ($formattedDate ?? '') . ')' }}
                        </p>
                        <h3 id="stat-admin-hadir" class="text-2xl font-bold text-emerald-600 mt-1">{{ $hadirHariIni ?? 0 }} Orang</h3>
                    </div>
                </div>
            </section>

            <!-- TAB 2: SCAN QR ABSENSI -->
            <section id="tab-admin-scan-qr" class="space-y-6 hidden">
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 max-w-xl mx-auto">
                    <div class="mb-4 text-center">
                        <h3 class="text-base font-bold text-slate-900">Scan QR / Barcode Kehadiran</h3>
                        <p class="text-xs text-slate-500 mt-1">Arahkan QR Code atlet atau pelatih ke kamera, atau gunakan scanner USB / input manual</p>
                    </div>

                    <div class="mb-4">
                        <div id="qr-reader" class="w-full rounded-2xl overflow-hidden border border-slate-200 bg-slate-900 min-h-[260px]"></div>
                        <p id="camera-status" class="text-xs text-slate-500 mt-2 text-center">Tekan tombol di bawah untuk menyalakan kamera.</p>
                        <div class="flex items-center justify-center gap-2 mt-3">
                            <button type="button" onclick="startCameraScanner()"
                                class="inline-flex items-center space-x-1.5 bg-[#0275d8] hover:bg-sky-600 text-white text-xs font-semibold px-4 py-2 rounded-xl transition shadow-sm">
                                <i class="fa-solid fa-camera"></i>
                                <span>Nyalakan Kamera</span>
                            </button>
                            <button type="button" onclick="stopCameraScanner()"
                                class="inline-flex items-center space-x-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold px-4 py-2 rounded-xl transition">
                                <i class="fa-solid fa-stop"></i>
                                <span>Matikan</span>
                            </button>
                        </div>
                    </div>

                    <form onsubmit="handleAdminScanBarcode(event)" class="space-y-3 text-xs border-t border-slate-100 pt-4">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Atau Input Token Manual / Scanner USB</label>
                            <input type="text" id="admin_scan_token" required placeholder="Contoh: TB-M-XXXXXX"
                                class="w-full px-3 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-emerald-500 font-mono uppercase">
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-5 py-2.5 rounded-xl transition shadow-sm">
                                Proses Token
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- TAB 3: KEHADIRAN ATLET -->
            <section id="tab-admin-kehadiran-atlet" class="space-y-6 hidden">
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Kehadiran Atlet — {{ $formattedDate ?? date('d F Y') }}</h3>
                            <p class="text-xs text-slate-500">Rekap presensi atlet pada tanggal terpilih</p>
                        </div>
                        <button type="button" onclick="openAdminPresensiModal('murid')"
                            class="inline-flex items-center justify-center space-x-2 bg-[#0275d8] hover:bg-sky-600 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition shadow-sm">
                            <i class="fa-solid fa-plus"></i>
                            <span>Input Presensi Atlet</span>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-400 uppercase font-semibold">
                                    <th class="pb-3 px-3">Nama Atlet</th>
                                    <th class="pb-3 px-3 text-center">Kelompok</th>
                                    <th class="pb-3 px-3">Jam Masuk</th>
                                    <th class="pb-3 px-3">Sesi</th>
                                    <th class="pb-3 px-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody id="tabel-admin-atlet-att" class="divide-y divide-slate-100 text-slate-700">
                                @forelse($athleteAttendances ?? [] as $att)
                                    @php
                                        $st = strtoupper($att->status);
                                        $badgeClass = match($st) {
                                            'HADIR' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'TELAT', 'ALFA' => 'bg-red-50 text-red-700 border-red-200',
                                            default => 'bg-amber-50 text-amber-700 border-amber-200',
                                        };
                                        $kel = $att->user->kelompok ?? 'Learn To Swim';
                                        $kelClass = $kel === 'Champions'
                                            ? 'bg-amber-50 text-amber-700 border-amber-200'
                                            : 'bg-sky-50 text-sky-700 border-sky-200';
                                    @endphp
                                    <tr>
                                        <td class="py-3 px-3 font-semibold text-slate-900">{{ $att->user->name ?? '-' }}</td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $kelClass }}">{{ $kel }}</span>
                                        </td>
                                        <td class="py-3 px-3 text-slate-500">{{ substr($att->time, 0, 5) }} WIB</td>
                                        <td class="py-3 px-3">{{ $att->session_name ?? 'Sesi Sore (15:30 WIB)' }}</td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold border {{ $badgeClass }}">{{ $st }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="empty-admin-atlet-att">
                                        <td colspan="5" class="py-6 text-center text-slate-400">Belum ada presensi atlet pada tanggal ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- TAB 4: KEHADIRAN PELATIH -->
            <section id="tab-admin-kehadiran-pelatih" class="space-y-6 hidden">
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Kehadiran Pelatih — {{ $formattedDate ?? date('d F Y') }}</h3>
                            <p class="text-xs text-slate-500">Rekap presensi pelatih pada tanggal terpilih</p>
                        </div>
                        <button type="button" onclick="openAdminPresensiModal('pelatih')"
                            class="inline-flex items-center justify-center space-x-2 bg-[#0275d8] hover:bg-sky-600 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition shadow-sm">
                            <i class="fa-solid fa-plus"></i>
                            <span>Input Presensi Pelatih</span>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-400 uppercase font-semibold">
                                    <th class="pb-3 px-3">Nama Pelatih</th>
                                    <th class="pb-3 px-3 text-center">Kelompok</th>
                                    <th class="pb-3 px-3">Jam Masuk</th>
                                    <th class="pb-3 px-3">Sesi</th>
                                    <th class="pb-3 px-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody id="tabel-admin-pelatih-att" class="divide-y divide-slate-100 text-slate-700">
                                @forelse($coachAttendances ?? [] as $att)
                                    @php
                                        $st = strtoupper($att->status);
                                        $badgeClass = match($st) {
                                            'HADIR' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'TELAT', 'ALFA' => 'bg-red-50 text-red-700 border-red-200',
                                            default => 'bg-amber-50 text-amber-700 border-amber-200',
                                        };
                                        $kel = $att->user->kelompok ?? 'Learn To Swim';
                                        $kelClass = $kel === 'Champions'
                                            ? 'bg-amber-50 text-amber-700 border-amber-200'
                                            : 'bg-sky-50 text-sky-700 border-sky-200';
                                    @endphp
                                    <tr>
                                        <td class="py-3 px-3 font-semibold text-slate-900">{{ $att->user->name ?? '-' }}</td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $kelClass }}">{{ $kel }}</span>
                                        </td>
                                        <td class="py-3 px-3 text-slate-500">{{ substr($att->time, 0, 5) }} WIB</td>
                                        <td class="py-3 px-3">{{ $att->session_name ?? 'Sesi Sore (15:30 WIB)' }}</td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold border {{ $badgeClass }}">{{ $st }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="empty-admin-pelatih-att">
                                        <td colspan="5" class="py-6 text-center text-slate-400">Belum ada presensi pelatih pada tanggal ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- TAB 5: DAFTAR EVENT -->
            <section id="tab-admin-event" class="space-y-6 hidden">
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Daftar Event & Kejuaraan Renang</h3>
                        <p class="text-xs text-slate-500">Jadwal kompetisi dan kejuaraan renang mendatang</p>
                    </div>
                    <button type="button" onclick="openAdminModal('modal-admin-event')"
                        class="inline-flex items-center justify-center space-x-2 bg-[#0275d8] hover:bg-sky-600 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition shadow-sm">
                        <i class="fa-solid fa-plus"></i>
                        <span>Tambah Event</span>
                    </button>
                </div>

                <div id="admin-daftar-event-container" class="space-y-3.5">
                    @forelse($events ?? [] as $ev)
                        @php
                            $start = \Carbon\Carbon::parse($ev->tanggal_mulai, 'Asia/Jakarta')->startOfDay();
                            $end   = $ev->tanggal_selesai ? \Carbon\Carbon::parse($ev->tanggal_selesai, 'Asia/Jakarta')->startOfDay() : $start->copy();
                            $dayNum = $start->format('d');
                            $monthShort = strtoupper($start->locale('id')->translatedFormat('M'));

                            if ($todayCarbon->between($start, $end)) {
                                $statusText = 'Sedang berlangsung';
                                $statusClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                            } elseif ($todayCarbon->lt($start)) {
                                $diff = $todayCarbon->diffInDays($start);
                                $statusText = $diff . ' hari lagi';
                                $statusClass = 'bg-sky-50 text-sky-700 border-sky-200';
                            } else {
                                $statusText = 'Selesai';
                                $statusClass = 'bg-slate-100 text-slate-500 border-slate-200';
                            }
                        @endphp
                        <div id="admin-card-event-{{ $ev->id }}" class="bg-white text-slate-800 rounded-2xl p-5 border border-slate-200 shadow-sm hover:border-sky-300 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-start sm:items-center gap-4">
                                <div class="w-14 h-14 rounded-xl bg-sky-50 border border-sky-200 flex flex-col items-center justify-center shrink-0">
                                    <span class="text-lg font-extrabold text-[#0275d8] leading-none">{{ $dayNum }}</span>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-sky-600 mt-1">{{ $monthShort }}</span>
                                </div>
                                <div>
                                    <h4 class="text-sm sm:text-base font-bold text-slate-900 leading-snug">{{ $ev->nama_event }}</h4>
                                    <p class="text-xs text-slate-500 mt-1">{{ $ev->tingkat }} · {{ $ev->lokasi }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2.5 self-end sm:self-center shrink-0">
                                <span class="px-3.5 py-1.5 rounded-full text-[11px] font-semibold border {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                                <button type="button" onclick="hapusAdminEvent({{ $ev->id }}, '{{ addslashes($ev->nama_event) }}')"
                                    class="bg-red-50 hover:bg-red-100 text-red-700 px-2.5 py-1.5 rounded-xl text-xs border border-red-200 transition" title="Hapus Event">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div id="admin-empty-event-card" class="bg-white rounded-3xl p-8 text-center text-slate-400 text-xs border border-slate-200">
                            Belum ada event kejuaraan yang terdaftar.
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- TAB 6: AKUN PELATIH -->
            <section id="tab-admin-akun-pelatih" class="space-y-6 hidden">
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Daftar Akun Pelatih</h3>
                            <p class="text-xs text-slate-500">Pelatih hanya dapat mengakses fitur sesuai kelompok yang ditetapkan</p>
                        </div>
                        <button type="button" onclick="openTambahAkun('pelatih')"
                            class="inline-flex items-center justify-center space-x-2 bg-[#0275d8] hover:bg-sky-600 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition shadow-sm">
                            <i class="fa-solid fa-user-plus"></i>
                            <span>Tambah Akun Pelatih</span>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-400 uppercase font-semibold">
                                    <th class="pb-3 px-3">Nama Pelatih</th>
                                    <th class="pb-3 px-3">Email</th>
                                    <th class="pb-3 px-3">No HP</th>
                                    <th class="pb-3 px-3 text-center">Kelompok</th>
                                    <th class="pb-3 px-3 text-center">Token Barcode</th>
                                    <th class="pb-3 px-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tabel-akun-pelatih" class="divide-y divide-slate-100 text-slate-700">
                                @forelse($coaches ?? [] as $c)
                                    @php
                                        $kel = $c->kelompok ?? 'Learn To Swim';
                                        $kelClass = $kel === 'Champions'
                                            ? 'bg-amber-50 text-amber-700 border-amber-200'
                                            : 'bg-sky-50 text-sky-700 border-sky-200';
                                    @endphp
                                    <tr id="row-user-{{ $c->id }}">
                                        <td class="py-3 px-3 font-semibold text-slate-900">{{ $c->name }}</td>
                                        <td class="py-3 px-3 text-slate-600">{{ $c->email }}</td>
                                        <td class="py-3 px-3 text-slate-600">{{ $c->phone ?? '-' }}</td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $kelClass }}">{{ $kel }}</span>
                                        </td>
                                        <td class="py-3 px-3 text-center font-mono text-slate-600">{{ $c->barcode_token ?? '-' }}</td>
                                        <td class="py-3 px-3 text-center space-x-1.5">
                                            <button type="button"
                                                onclick="editAkun({{ $c->id }}, '{{ addslashes($c->name) }}', '{{ addslashes($c->email) }}', '{{ addslashes($c->phone ?? '') }}', 'pelatih', '{{ addslashes($kel) }}')"
                                                class="bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 px-2.5 py-1 rounded-lg text-[11px] font-semibold transition">
                                                Edit
                                            </button>
                                            <button type="button"
                                                onclick="hapusAkun({{ $c->id }}, '{{ addslashes($c->name) }}')"
                                                class="bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 px-2.5 py-1 rounded-lg text-[11px] font-semibold transition">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="empty-akun-pelatih">
                                        <td colspan="6" class="py-6 text-center text-slate-400">Belum ada akun pelatih terdaftar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- TAB 7: AKUN ATLET (MURID) -->
            <section id="tab-admin-akun-atlet" class="space-y-6 hidden">
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Daftar Akun Atlet (Murid)</h3>
                            <p class="text-xs text-slate-500">Kelola akun atlet beserta kelompok latihannya</p>
                        </div>
                        <button type="button" onclick="openTambahAkun('murid')"
                            class="inline-flex items-center justify-center space-x-2 bg-[#0275d8] hover:bg-sky-600 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition shadow-sm">
                            <i class="fa-solid fa-user-plus"></i>
                            <span>Tambah Akun Atlet</span>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-400 uppercase font-semibold">
                                    <th class="pb-3 px-3">Nama Atlet</th>
                                    <th class="pb-3 px-3">Email</th>
                                    <th class="pb-3 px-3">No HP</th>
                                    <th class="pb-3 px-3 text-center">Kelompok</th>
                                    <th class="pb-3 px-3 text-center">Token Barcode</th>
                                    <th class="pb-3 px-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tabel-akun-murid" class="divide-y divide-slate-100 text-slate-700">
                                @forelse($athletes ?? [] as $a)
                                    @php
                                        $kel = $a->kelompok ?? 'Learn To Swim';
                                        $kelClass = $kel === 'Champions'
                                            ? 'bg-amber-50 text-amber-700 border-amber-200'
                                            : 'bg-sky-50 text-sky-700 border-sky-200';
                                    @endphp
                                    <tr id="row-user-{{ $a->id }}">
                                        <td class="py-3 px-3 font-semibold text-slate-900">{{ $a->name }}</td>
                                        <td class="py-3 px-3 text-slate-600">{{ $a->email }}</td>
                                        <td class="py-3 px-3 text-slate-600">{{ $a->phone ?? '-' }}</td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $kelClass }}">{{ $kel }}</span>
                                        </td>
                                        <td class="py-3 px-3 text-center font-mono text-slate-600">{{ $a->barcode_token ?? '-' }}</td>
                                        <td class="py-3 px-3 text-center space-x-1.5">
                                            <button type="button"
                                                onclick="editAkun({{ $a->id }}, '{{ addslashes($a->name) }}', '{{ addslashes($a->email) }}', '{{ addslashes($a->phone ?? '') }}', 'murid', '{{ addslashes($kel) }}')"
                                                class="bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 px-2.5 py-1 rounded-lg text-[11px] font-semibold transition">
                                                Edit
                                            </button>
                                            <button type="button"
                                                onclick="hapusAkun({{ $a->id }}, '{{ addslashes($a->name) }}')"
                                                class="bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 px-2.5 py-1 rounded-lg text-[11px] font-semibold transition">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="empty-akun-murid">
                                        <td colspan="6" class="py-6 text-center text-slate-400">Belum ada akun atlet terdaftar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- MODAL 1: TAMBAH / EDIT AKUN -->
    <div id="modal-tambah-akun" onclick="if(event.target === this) closeAdminModal('modal-tambah-akun')" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-3xl p-6 max-w-lg w-full shadow-2xl border border-slate-200 relative my-8">
            <button type="button" onclick="closeAdminModal('modal-tambah-akun')" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
            <h3 id="modal-akun-title" class="text-base font-bold text-slate-900 mb-1">Tambah Akun Baru</h3>
            <p class="text-xs text-slate-500 mb-4">Daftarkan atau perbarui akun Pelatih / Atlet beserta kelompok latihannya</p>

            <form id="form-tambah-akun" onsubmit="handleSimpanAkun(event)" class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 text-xs">
                <input type="hidden" id="akun_id" value="">

                <div class="sm:col-span-2">
                    <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" id="akun_nama" required placeholder="Contoh: Coach Dimas"
                        class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Email</label>
                    <input type="email" id="akun_email" required placeholder="nama@tirta.com"
                        class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">No HP</label>
                    <input type="text" id="akun_phone" placeholder="08xxxxxxxxxx"
                        class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Role</label>
                    <select id="akun_role" onchange="toggleFieldKelompok()" required class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                        <option value="pelatih">Pelatih</option>
                        <option value="murid">Atlet (Murid)</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div id="wrap_akun_kelompok">
                    <label class="block font-semibold text-slate-700 mb-1">Kelompok Latihan</label>
                    <select id="akun_kelompok" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                        <option value="Learn To Swim">Learn To Swim</option>
                        <option value="Intermediate">Intermediate</option>
                        <option value="Champions">Champions</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label id="label_akun_password" class="block font-semibold text-slate-700 mb-1">Password</label>
                    <input type="password" id="akun_password" required minlength="6" placeholder="Minimal 6 karakter"
                        class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                </div>

                <div class="sm:col-span-2 flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeAdminModal('modal-tambah-akun')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-4 py-2 rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" id="btn-simpan-akun" class="bg-[#0275d8] hover:bg-sky-600 text-white font-semibold px-5 py-2 rounded-xl transition">
                        Simpan Akun
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: TAMBAH EVENT ADMIN -->
    <div id="modal-admin-event" onclick="if(event.target === this) closeAdminModal('modal-admin-event')" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-3xl p-6 max-w-lg w-full shadow-2xl border border-slate-200 relative my-8">
            <button type="button" onclick="closeAdminModal('modal-admin-event')" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
            <h3 class="text-base font-bold text-slate-900 mb-1">Tambah Event Kejuaraan</h3>
            <p class="text-xs text-slate-500 mb-4">Jadwal event akan tampil di dashboard seluruh pelatih, murid, dan admin</p>

            <form id="form-admin-event" onsubmit="handleAdminSimpanEvent(event)" class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 text-xs">
                <div class="sm:col-span-2">
                    <label class="block font-semibold text-slate-700 mb-1">Nama Event</label>
                    <input type="text" id="admin_ev_nama" required placeholder="Contoh: IOAC (Indonesia Open Aquatic Championship) 2026 – Renang"
                        class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tingkat</label>
                    <select id="admin_ev_tingkat" required class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                        <option value="Kabupaten/Kota">Kabupaten/Kota</option>
                        <option value="Provinsi">Provinsi</option>
                        <option value="Nasional">Nasional</option>
                        <option value="Internasional">Internasional</option>
                        <option value="Klub / Internal">Klub / Internal</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Lokasi</label>
                    <input type="text" id="admin_ev_lokasi" required placeholder="Contoh: Stadion Akuatik GBK, Jakarta"
                        class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Mulai</label>
                    <input type="date" id="admin_ev_tgl_mulai" required value="{{ date('Y-m-d') }}"
                        class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Selesai (Opsional)</label>
                    <input type="date" id="admin_ev_tgl_selesai"
                        class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                </div>

                <div class="sm:col-span-2 flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeAdminModal('modal-admin-event')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-4 py-2 rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="bg-[#0275d8] hover:bg-sky-600 text-white font-semibold px-5 py-2 rounded-xl transition">
                        Simpan Event
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: INPUT PRESENSI MANUAL ADMIN -->
    <div id="modal-admin-presensi" onclick="if(event.target === this) closeAdminModal('modal-admin-presensi')" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-slate-200 relative">
            <button type="button" onclick="closeAdminModal('modal-admin-presensi')" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
            <h3 id="modal-presensi-title" class="text-base font-bold text-slate-900 mb-1">Input Presensi Manual</h3>
            <p id="modal-presensi-subtitle" class="text-xs text-slate-500 mb-4">Catat kehadiran secara manual ke database</p>

            <form onsubmit="handleAdminManualAbsen(event)" class="space-y-3 text-xs">
                <div>
                    <label id="label-admin-manual-nama" class="block font-semibold text-slate-700 mb-1">Nama</label>
                    <select id="admin_manual_nama" required class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                        @foreach($athletes ?? [] as $a)
                            <option value="{{ $a->name }}" data-role="murid">{{ $a->name }} ({{ $a->kelompok ?? 'Learn To Swim' }})</option>
                        @endforeach
                        @foreach($coaches ?? [] as $c)
                            <option value="{{ $c->name }}" data-role="pelatih">{{ $c->name }} ({{ $c->kelompok ?? 'Learn To Swim' }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Sesi Latihan</label>
                    <select id="admin_manual_sesi" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                        <option value="Sesi Sore (15:30 WIB)">Sesi Sore (15:30 WIB)</option>
                        <option value="Sesi Pagi (06:00 WIB)">Sesi Pagi (06:00 WIB)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Status</label>
                    <select id="admin_manual_status" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                        <option value="HADIR">Hadir</option>
                        <option value="TELAT">Telat</option>
                        <option value="IZIN">Izin</option>
                        <option value="SAKIT">Sakit</option>
                        <option value="ALFA">Alfa</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeAdminModal('modal-admin-presensi')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-4 py-2 rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="bg-[#0275d8] hover:bg-sky-600 text-white font-semibold px-5 py-2 rounded-xl transition">
                        Simpan Presensi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript Admin Terpisah -->
    <script src="/js/admin-dashboard.js"></script>
</body>
</html>
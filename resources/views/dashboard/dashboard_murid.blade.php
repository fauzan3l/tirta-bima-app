@php
    /** @var \Illuminate\Support\Collection<int, \App\Models\Attendance> $myAttendances */
    /** @var \Illuminate\Support\Collection<int, \App\Models\BestTime> $myBestTimes */
    /** @var \Illuminate\Support\Collection<int, \App\Models\Event> $events */
    $muridKelompok = $muridKelompok ?? (Auth::user()->kelompok ?? 'Learn To Swim');
    $isChampions   = $muridKelompok === 'Champions';
    $todayCarbon   = \Carbon\Carbon::now('Asia/Jakarta')->startOfDay();
    $logoUrl       = asset('images/logo-tbsc.jpeg');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tirta Bima — Dashboard Atlet</title>
    <link rel="icon" type="images/jpeg" href="{{ $logoUrl }}">

    <!-- Tailwind CSS & Font Awesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS Terpisah -->
    <link rel="stylesheet" href="/css/murid-dashboard.css">
</head>
<body class="bg-slate-100 min-h-screen font-sans antialiased text-slate-800 flex flex-col md:flex-row">

    <!-- OVERLAY MOBILE -->
    <div id="murid-sidebar-overlay" onclick="toggleMuridSidebar(false)"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-30 hidden md:hidden"></div>

    <!-- SIDEBAR MURID (DRAWER DI HP, STICKY DI DESKTOP) -->
    <aside id="murid-sidebar"
        class="fixed inset-y-0 left-0 w-72 bg-[#0b2447] text-white flex flex-col justify-between shrink-0 h-screen overflow-y-auto shadow-xl z-40 transform -translate-x-full transition-transform duration-200 md:translate-x-0 md:sticky md:top-0 md:z-20">
        <div>
            <div class="p-6 border-b border-slate-700/60 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <img src="{{ $logoUrl }}" alt="Logo Tirta Bima"
                        class="w-10 h-10 rounded-xl object-contain bg-white p-1 shrink-0 shadow-sm">
                    <div>
                        <span class="text-base font-bold tracking-wide block leading-tight">Tirta Bima</span>
                        <span class="text-[10px] uppercase font-semibold text-sky-400 tracking-wider">Swimming Club</span>
                    </div>
                </div>
                <button type="button" onclick="toggleMuridSidebar(false)" class="md:hidden text-slate-300 hover:text-white p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Profil User -->
            <div class="px-6 py-5 border-b border-slate-700/40 bg-white/5">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-sky-500/20 border border-sky-400 flex items-center justify-center text-sky-300 font-bold text-sm shrink-0">
                        {{ strtoupper(substr(Auth::user()->name ?? 'M', 0, 1)) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-xs font-semibold text-white truncate">{{ Auth::user()->name ?? 'Member Tirta' }}</p>
                        <span class="inline-block mt-1 px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wide border {{ $isChampions ? 'bg-amber-400/20 text-amber-300 border-amber-400/40' : 'bg-emerald-400/20 text-emerald-300 border-emerald-400/30' }}">
                            Kelompok {{ $muridKelompok }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Menu Sidebar -->
            <nav class="p-4 space-y-1.5 text-xs font-medium">
                <button onclick="switchTab('beranda')" id="btn-beranda" class="nav-tab is-active w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition bg-[#0275d8] text-white">
                    <i class="fa-solid fa-table-columns text-sm w-5 text-center"></i>
                    <span>Beranda Dashboard</span>
                </button>
                <button onclick="switchTab('kehadiran-atlet')" id="btn-kehadiran-atlet" class="nav-tab is-inactive w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition text-slate-300 hover:bg-white/10 hover:text-white">
                    <i class="fa-solid fa-qrcode text-sm w-5 text-center"></i>
                    <span>Kartu QR & Riwayat</span>
                </button>
                <button onclick="switchTab('best-time')" id="btn-best-time" class="nav-tab is-inactive w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition text-slate-300 hover:bg-white/10 hover:text-white">
                    <i class="fa-solid fa-stopwatch text-sm w-5 text-center text-amber-400"></i>
                    <span>Best Time Saya</span>
                </button>
                <button onclick="switchTab('event')" id="btn-event" class="nav-tab is-inactive w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition text-slate-300 hover:bg-white/10 hover:text-white">
                    <i class="fa-solid fa-trophy text-sm w-5 text-center text-sky-400"></i>
                    <span>Daftar Event</span>
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

    <!-- KONTEN UTAMA -->
    <div class="flex-1 flex flex-col min-h-screen min-w-0">
        <header class="bg-white border-b border-slate-200 px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between sticky top-0 z-10 gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <button type="button" onclick="toggleMuridSidebar(true)"
                    class="md:hidden shrink-0 w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="min-w-0">
                    <h1 id="page-title" class="text-lg font-bold text-slate-900 truncate">Beranda Saya</h1>
                    <p id="page-subtitle" class="text-xs text-slate-500 truncate">Ringkasan aktivitas dan presensi latihan</p>
                </div>
            </div>
            <div class="shrink-0 text-xs text-slate-500 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200">
                <i class="fa-regular fa-calendar-check text-sky-600 mr-1"></i> {{ $formattedDate ?? date('d F Y') }}
            </div>
        </header>

        <main class="p-4 sm:p-6 lg:p-8 flex-1 space-y-6">
            <!-- TAB 1: BERANDA -->
            <section id="tab-beranda" class="dashboard-panel space-y-6">
                <div class="bg-[#0b2447] text-white rounded-3xl p-8 relative overflow-hidden shadow-lg">
                    <div class="relative z-10 max-w-2xl">
                        <span class="text-xs font-bold uppercase tracking-widest text-sky-400">Portal Atlet · Kelompok {{ $muridKelompok }}</span>
                        <h2 class="text-2xl sm:text-3xl font-extrabold mt-1">Halo, {{ Auth::user()->name }}!</h2>
                        <p class="text-slate-300 text-xs sm:text-sm mt-2 leading-relaxed">
                            Latihan sore dimulai pukul <strong>15:30 WIB</strong>. Pastikan sudah melakukan scan barcode sebelum jam tersebut agar tidak terhitung telat.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                        <p class="text-[11px] font-semibold text-slate-500 uppercase">Total Hadir</p>
                        <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ $totalHadir ?? 0 }} Sesi</h3>
                    </div>
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                        <p class="text-[11px] font-semibold text-slate-500 uppercase">Izin / Sakit</p>
                        <h3 class="text-2xl font-bold text-amber-600 mt-1">{{ $totalIzinSakit ?? 0 }} Kali</h3>
                    </div>
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                        <p class="text-[11px] font-semibold text-slate-500 uppercase">Catatan Best Time</p>
                        <h3 class="text-2xl font-bold text-sky-600 mt-1">{{ ($myBestTimes ?? collect())->count() }} Rekor</h3>
                    </div>
                </div>
            </section>

            <!-- TAB 2: KARTU QR & RIWAYAT -->
            <section id="tab-kehadiran-atlet" class="dashboard-panel space-y-6 hidden">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Kartu QR -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 text-center flex flex-col items-center justify-between">
                        <div>
                            <span class="text-[10px] font-bold text-sky-600 uppercase tracking-widest">Kartu Barcode Absensi</span>
                            <h4 class="text-base font-bold text-slate-900 mt-1">{{ Auth::user()->name }}</h4>
                            <p class="text-xs text-slate-400 mb-4 font-mono">{{ Auth::user()->barcode_token ?? ('TB-ATLET-'.Auth::id()) }}</p>

                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 inline-block mb-3">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode(Auth::user()->barcode_token ?? ('TB-ATLET-'.Auth::id())) }}"
                                     alt="QR Code Saya" class="w-44 h-44 mx-auto rounded-lg">
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Arahkan kode QR ini ke kamera scanner sebelum pukul <strong>15:30 WIB</strong>.
                        </p>
                    </div>

                    <!-- Riwayat Presensi -->
                    <div class="lg:col-span-2 bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                        <h3 class="text-base font-bold text-slate-900 mb-3">Riwayat Kehadiran Terakhir</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-100 text-slate-400 uppercase font-semibold">
                                        <th class="pb-3 px-3">Tanggal</th>
                                        <th class="pb-3 px-3">Waktu</th>
                                        <th class="pb-3 px-3">Sesi</th>
                                        <th class="pb-3 px-3 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700">
                                    @forelse($myAttendances ?? [] as $att)
                                        @php
                                            $st = strtoupper($att->status);
                                            $badgeClass = match($st) {
                                                'HADIR' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                'TELAT', 'ALFA' => 'bg-red-50 text-red-700 border-red-200',
                                                default => 'bg-amber-50 text-amber-700 border-amber-200',
                                            };
                                        @endphp
                                        <tr>
                                            <td class="py-3 px-3 font-semibold text-slate-900">{{ \Carbon\Carbon::parse($att->date)->locale('id')->translatedFormat('d M Y') }}</td>
                                            <td class="py-3 px-3 text-slate-500">{{ substr($att->time, 0, 5) }} WIB</td>
                                            <td class="py-3 px-3">{{ $att->session_name ?? 'Sesi Sore (15:30 WIB)' }}</td>
                                            <td class="py-3 px-3 text-center">
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold border {{ $badgeClass }}">{{ $st }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-6 text-center text-slate-400">Belum ada riwayat kehadiran tercatat.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <!-- TAB 3: BEST TIME SAYA (READ-ONLY) -->
            <section id="tab-best-time" class="dashboard-panel space-y-6 hidden">
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                    <div class="mb-4">
                        <h3 class="text-base font-bold text-slate-900">Catatan Best Time Saya</h3>
                        <p class="text-xs text-slate-500">Rekam jejak waktu terbaikmu yang dicatat oleh pelatih</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-400 uppercase font-semibold">
                                    <th class="pb-3 px-3">Gaya Renang</th>
                                    <th class="pb-3 px-3 text-center">Jarak</th>
                                    <th class="pb-3 px-3 text-center">Best Time</th>
                                    <th class="pb-3 px-3">Tanggal</th>
                                    <th class="pb-3 px-3">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @forelse($myBestTimes ?? [] as $bt)
                                    <tr>
                                        <td class="py-3 px-3 font-semibold text-slate-900">{{ $bt->gaya }}</td>
                                        <td class="py-3 px-3 text-center">{{ $bt->jarak }}</td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="px-2.5 py-1 rounded-full text-[11px] font-mono font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                {{ $bt->waktu }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 text-slate-500">{{ $bt->tanggal }}</td>
                                        <td class="py-3 px-3 text-slate-600">{{ $bt->keterangan ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-6 text-center text-slate-400">Belum ada catatan Best Time untuk akunmu.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- TAB 4: DAFTAR EVENT (READ-ONLY) -->
            <section id="tab-event" class="dashboard-panel space-y-6 hidden">
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                    <h3 class="text-base font-bold text-slate-900">Daftar Event & Kejuaraan Renang</h3>
                    <p class="text-xs text-slate-500">Jadwal kompetisi dan kejuaraan renang mendatang</p>
                </div>

                <div class="space-y-3.5">
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
                        <div class="bg-white text-slate-800 rounded-2xl p-5 border border-slate-200 shadow-sm hover:border-sky-300 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
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
                            <div class="self-end sm:self-center shrink-0">
                                <span class="px-3.5 py-1.5 rounded-full text-[11px] font-semibold border {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-3xl p-8 text-center text-slate-400 text-xs border border-slate-200">
                            Belum ada event kejuaraan yang terdaftar.
                        </div>
                    @endforelse
                </div>
            </section>
        </main>
    </div>

    <!-- JavaScript Terpisah -->
    <script src="/js/murid-dashboard.js"></script>
</body>
</html>
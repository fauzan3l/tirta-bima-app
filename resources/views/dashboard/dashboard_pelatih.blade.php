@php
    /** @var \Illuminate\Support\Collection<int, \App\Models\User> $athletes */
    /** @var \Illuminate\Support\Collection<int, \App\Models\Attendance> $athleteAttendances */
    /** @var \Illuminate\Support\Collection<int, \App\Models\DataAtlet> $dataAtlet */
    /** @var \Illuminate\Support\Collection<int, \App\Models\ProgramLatihan> $programs */
    /** @var \Illuminate\Support\Collection<int, \App\Models\BestTime> $bestTimes */
    /** @var \Illuminate\Support\Collection<int, \App\Models\Event> $events */
    $coachKelompok = $coachKelompok ?? (Auth::user()->kelompok ?? 'Learn To Swim');
    $isChampions   = $isChampions ?? ($coachKelompok === 'Champions');
    $todayCarbon   = \Carbon\Carbon::now('Asia/Jakarta')->startOfDay();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tirta Bima — Dashboard Pelatih</title>

    <!-- Tailwind CSS & Font Awesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS Terpisah -->
    <link rel="stylesheet" href="/css/pelatih-dashboard.css">
</head>
<body class="bg-slate-100 min-h-screen font-sans antialiased text-slate-800 flex flex-col md:flex-row">

    <!-- SIDEBAR PELATIH (STICKY) -->
    <aside class="w-full md:w-72 bg-[#0b2447] text-white flex flex-col justify-between shrink-0 md:h-screen md:sticky md:top-0 overflow-y-auto shadow-xl z-20">
        <div>
            <div class="p-6 border-b border-slate-700/60">
                <div class="flex items-center space-x-3">
                    <svg class="w-7 h-7 text-sky-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <div>
                        <span class="text-base font-bold tracking-wide block leading-tight">Tirta Bima</span>
                        <span class="text-[10px] uppercase font-semibold text-sky-400 tracking-wider">Swimming Club</span>
                    </div>
                </div>
            </div>

            <!-- Profil User -->
            <div class="px-6 py-5 border-b border-slate-700/40 bg-white/5">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-sky-500/20 border border-sky-400 flex items-center justify-center text-sky-300 font-bold text-sm shrink-0">
                        {{ strtoupper(substr(Auth::user()->name ?? 'P', 0, 1)) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-xs font-semibold text-white truncate">{{ Auth::user()->name ?? 'Coach' }}</p>
                        <span class="inline-block mt-1 px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wide {{ $isChampions ? 'bg-amber-400/20 text-amber-300 border border-amber-400/40' : 'bg-sky-400/20 text-sky-300 border border-sky-400/30' }}">
                            Kelompok {{ $coachKelompok }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Menu Sidebar -->
            <nav class="p-4 space-y-1.5 text-xs font-medium">
                <button onclick="switchTab('beranda')" id="btn-beranda" class="nav-tab is-active w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition bg-[#0275d8] text-white">
                    <i class="fa-solid fa-table-columns text-sm w-5 text-center"></i>
                    <span>Beranda Pelatih</span>
                </button>
                <button onclick="switchTab('kehadiran-atlet')" id="btn-kehadiran-atlet" class="nav-tab is-inactive w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition text-slate-300 hover:bg-white/10 hover:text-white">
                    <i class="fa-solid fa-clipboard-user text-sm w-5 text-center"></i>
                    <span>Kehadiran Atlet</span>
                </button>
                <button onclick="switchTab('data-atlet')" id="btn-data-atlet" class="nav-tab is-inactive w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition text-slate-300 hover:bg-white/10 hover:text-white">
                    <i class="fa-solid fa-id-card text-sm w-5 text-center"></i>
                    <span>Data Atlet</span>
                </button>
                <button onclick="switchTab('event')" id="btn-event" class="nav-tab is-inactive w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition text-slate-300 hover:bg-white/10 hover:text-white">
                    <i class="fa-solid fa-trophy text-sm w-5 text-center text-sky-400"></i>
                    <span>Daftar Event</span>
                </button>

                @if($isChampions)
                    <button onclick="switchTab('program-latihan')" id="btn-program-latihan" class="nav-tab is-inactive w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition text-slate-300 hover:bg-white/10 hover:text-white">
                        <i class="fa-solid fa-dumbbell text-sm w-5 text-center text-amber-400"></i>
                        <span>Program Latihan</span>
                    </button>
                    <button onclick="switchTab('best-time')" id="btn-best-time" class="nav-tab is-inactive w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition text-slate-300 hover:bg-white/10 hover:text-white">
                        <i class="fa-solid fa-stopwatch text-sm w-5 text-center text-amber-400"></i>
                        <span>Best Time</span>
                    </button>
                @endif
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

    <!-- KONTEN UTAMA PELATIH -->
    <div class="flex-1 flex flex-col min-h-screen min-w-0">
        <header class="bg-white border-b border-slate-200 px-6 lg:px-8 py-4 flex items-center justify-between sticky top-0 z-10">
            <div>
                <h1 id="page-title" class="text-lg font-bold text-slate-900">Beranda Pelatih</h1>
                <p id="page-subtitle" class="text-xs text-slate-500">Monitoring atlet bimbingan & jadwal sesi renang</p>
            </div>
            <div onclick="bukaKalenderTanggal()" title="Klik untuk ganti tanggal & lihat riwayat"
                class="relative cursor-pointer select-none flex items-center space-x-2 text-xs text-slate-600 bg-slate-50 hover:bg-sky-50 hover:border-sky-300 px-3.5 py-2 rounded-xl border border-slate-200 transition">
                <i class="fa-regular fa-calendar-check text-sky-600"></i>
                <span class="font-semibold">{{ $formattedDate ?? date('d F Y') }}</span>
                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                <input type="date" id="filter-tanggal"
                    value="{{ $selectedDate ?? date('Y-m-d') }}"
                    onchange="gantiTanggalRiwayat(this.value)"
                    class="sr-only">
            </div>
        </header>

        <main class="p-6 lg:p-8 flex-1 space-y-6">
            <!-- TAB 1: BERANDA PELATIH -->
            <section id="tab-beranda" class="dashboard-panel space-y-6">
                <div class="bg-[#0b2447] text-white rounded-3xl p-8 relative overflow-hidden shadow-lg">
                    <div class="relative z-10 max-w-2xl">
                        <span class="text-xs font-bold uppercase tracking-widest text-sky-400">Portal Instruktur & Pelatih</span>
                        <h2 class="text-2xl sm:text-3xl font-extrabold mt-1">Semangat Melatih, {{ Auth::user()->name ?? 'Coach' }}!</h2>
                        <p class="text-slate-300 text-xs sm:text-sm mt-2 leading-relaxed">
                            @if($isChampions)
                                Akses penuh kelompok Champions: pantau kehadiran, data atlet, daftar event, program latihan, dan catatan best time.
                            @else
                                Kelompok bimbingan {{ $coachKelompok }}: pantau kedisiplinan kehadiran, jadwal event, dan kelola biodata atletmu.
                            @endif
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                        <p class="text-[11px] font-semibold text-slate-500 uppercase">Total Data Atlet</p>
                        <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ ($dataAtlet ?? collect())->count() }} Atlet</h3>
                    </div>
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                        <p class="text-[11px] font-semibold text-slate-500 uppercase">
                            {{ ($selectedDate ?? date('Y-m-d')) === date('Y-m-d') ? 'Hadir Hari Ini' : 'Hadir Hari ini' }}
                        </p>
                        <h3 id="stat-hadir-sore-ini" class="text-2xl font-bold text-emerald-600 mt-1">{{ $hadirHariIni ?? 0 }} Atlet</h3>
                    </div>
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                        <p class="text-[11px] font-semibold text-slate-500 uppercase">Jadwal Sesi</p>
                        <h3 class="text-2xl font-bold text-sky-600 mt-1">15:30 WIB</h3>
                    </div>
                </div>
            </section>

            <!-- TAB 2: KEHADIRAN ATLET -->
            <section id="tab-kehadiran-atlet" class="dashboard-panel space-y-6 hidden">
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Catatan Kehadiran Atlet — {{ $formattedDate ?? date('d F Y') }}</h3>
                            <p class="text-xs text-slate-500">Daftar presensi atlet bimbingan pada tanggal terpilih</p>
                        </div>
                        <button type="button" onclick="openModal('modal-kehadiran')"
                            class="inline-flex items-center justify-center space-x-2 bg-[#0275d8] hover:bg-sky-600 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition shadow-sm">
                            <i class="fa-solid fa-plus"></i>
                            <span>Input Presensi</span>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-400 uppercase font-semibold">
                                    <th class="pb-3 px-3">Nama Atlet</th>
                                    <th class="pb-3 px-3">Jam Masuk</th>
                                    <th class="pb-3 px-3">Sesi</th>
                                    <th class="pb-3 px-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody id="tabel-presensi-pelatih-murid-body" class="divide-y divide-slate-100 text-slate-700">
                                @forelse($athleteAttendances ?? [] as $att)
                                    @php
                                        $st = strtoupper($att->status);
                                        $badgeClass = match($st) {
                                            'HADIR' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'TELAT', 'ALFA' => 'bg-red-50 text-red-700 border-red-200',
                                            default => 'bg-amber-50 text-amber-700 border-amber-200',
                                        };
                                    @endphp
                                    <tr>
                                        <td class="py-3 px-3 font-semibold text-slate-900">{{ $att->user->name ?? '-' }}</td>
                                        <td class="py-3 px-3 text-slate-500">{{ substr($att->time, 0, 5) }} WIB</td>
                                        <td class="py-3 px-3">{{ $att->session_name ?? 'Sesi Sore (15:30 WIB)' }}</td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold border {{ $badgeClass }}">{{ $st }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="empty-pelatih-murid-row">
                                        <td colspan="4" class="py-6 text-center text-slate-400">Belum ada catatan kehadiran atlet pada tanggal ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- TAB 3: DATA ATLET -->
            <section id="tab-data-atlet" class="dashboard-panel space-y-6 hidden">
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Daftar Data Atlet — {{ $coachKelompok }}</h3>
                            <p class="text-xs text-slate-500">Biodata lengkap, data fisik, serta sekolah dan kelas atlet</p>
                        </div>
                        <button type="button" onclick="openTambahDataAtlet()"
                            class="inline-flex items-center justify-center space-x-2 bg-[#0275d8] hover:bg-sky-600 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition shadow-sm">
                            <i class="fa-solid fa-plus"></i>
                            <span>Input Data Atlet</span>
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-400 uppercase font-semibold">
                                    <th class="pb-3 px-3">Nama</th>
                                    <th class="pb-3 px-3">NIK</th>
                                    <th class="pb-3 px-3">NISNAS</th>
                                    <th class="pb-3 px-3">Tempat Tanggal Lahir</th>
                                    <th class="pb-3 px-3">No HP</th>
                                    <th class="pb-3 px-3">Gmail</th>
                                    <th class="pb-3 px-3 text-center">Tinggi Badan</th>
                                    <th class="pb-3 px-3 text-center">Berat Badan</th>
                                    <th class="pb-3 px-3">Sekolah</th>
                                    <th class="pb-3 px-3 text-center">Kelas</th>
                                    <th class="pb-3 px-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tabel-data-atlet-body" class="divide-y divide-slate-100 text-slate-700">
                                @forelse($dataAtlet ?? [] as $d)
                                    @php /** @var \App\Models\DataAtlet $d */ @endphp
                                    <tr id="row-data-atlet-{{ $d->id }}">
                                        <td class="py-3 px-3 font-semibold text-slate-900">{{ $d->name }}</td>
                                        <td class="py-3 px-3 font-mono text-slate-600">{{ $d->nik ?? '-' }}</td>
                                        <td class="py-3 px-3 font-mono text-slate-600">{{ $d->nisnas ?? '-' }}</td>
                                        <td class="py-3 px-3 text-slate-600">{{ $d->ttl ?? '-' }}</td>
                                        <td class="py-3 px-3 text-slate-600">{{ $d->phone ?? '-' }}</td>
                                        <td class="py-3 px-3 text-slate-600">{{ $d->email ?? '-' }}</td>
                                        <td class="py-3 px-3 text-center text-slate-700">{{ $d->tinggi_badan ? $d->tinggi_badan . ' cm' : '-' }}</td>
                                        <td class="py-3 px-3 text-center text-slate-700">{{ $d->berat_badan ? $d->berat_badan . ' kg' : '-' }}</td>
                                        <td class="py-3 px-3 text-slate-700">{{ $d->sekolah ?? '-' }}</td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-sky-50 text-sky-700 border border-sky-200">
                                                {{ $d->kelas ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 text-center space-x-1.5">
                                            <button type="button"
                                                onclick="editDataAtlet({{ $d->id }}, '{{ addslashes($d->name ?? '') }}', '{{ addslashes($d->nik ?? '') }}', '{{ addslashes($d->nisnas ?? '') }}', '{{ addslashes($d->ttl ?? '') }}', '{{ addslashes($d->phone ?? '') }}', '{{ addslashes($d->email ?? '') }}', '{{ $d->tinggi_badan ?? '' }}', '{{ $d->berat_badan ?? '' }}', '{{ addslashes($d->sekolah ?? '') }}', '{{ addslashes($d->kelas ?? '') }}')"
                                                class="bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 px-2.5 py-1 rounded-lg text-[11px] font-semibold transition">
                                                Edit
                                            </button>
                                            <button type="button"
                                                onclick="hapusDataAtlet({{ $d->id }}, '{{ addslashes($d->name ?? '') }}')"
                                                class="bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 px-2.5 py-1 rounded-lg text-[11px] font-semibold transition">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="empty-data-atlet-row">
                                        <td colspan="11" class="py-6 text-center text-slate-400">Belum ada data atlet pada kelompok {{ $coachKelompok }}.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- TAB 4: DAFTAR EVENT (TEMA TERANG SESUAI DASHBOARD) -->
            <section id="tab-event" class="dashboard-panel space-y-6 hidden">
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Daftar Event & Kejuaraan Renang</h3>
                        <p class="text-xs text-slate-500">Jadwal kompetisi dan kejuaraan renang mendatang</p>
                    </div>
                    @if($isChampions)
                        <button type="button" onclick="openModal('modal-event')"
                            class="inline-flex items-center justify-center space-x-2 bg-[#0275d8] hover:bg-sky-600 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition shadow-sm">
                            <i class="fa-solid fa-plus"></i>
                            <span>Tambah Event</span>
                        </button>
                    @endif
                </div>

                <div id="daftar-event-container" class="space-y-3.5">
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
                        <div id="card-event-{{ $ev->id }}" class="bg-white text-slate-800 rounded-2xl p-5 border border-slate-200 shadow-sm hover:border-sky-300 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
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
                                @if($isChampions)
                                    <button type="button" onclick="hapusEvent({{ $ev->id }}, '{{ addslashes($ev->nama_event) }}')"
                                        class="bg-red-50 hover:bg-red-100 text-red-700 px-2.5 py-1.5 rounded-xl text-xs border border-red-200 transition" title="Hapus Event">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div id="empty-event-card" class="bg-white rounded-3xl p-8 text-center text-slate-400 text-xs border border-slate-200">
                            Belum ada event kejuaraan yang terdaftar.
                        </div>
                    @endforelse
                </div>
            </section>

            @if($isChampions)
                <!-- TAB 5: PROGRAM LATIHAN (TEMA TERANG SESUAI DASHBOARD) -->
                <section id="tab-program-latihan" class="dashboard-panel space-y-6 hidden">
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Program Latihan — Champions</h3>
                            <p class="text-xs text-slate-500">Program latihan mingguan khusus kelompok Champions beserta file unduhan</p>
                        </div>
                        <button type="button" onclick="openModal('modal-program')"
                            class="inline-flex items-center justify-center space-x-2 bg-[#0275d8] hover:bg-sky-600 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition shadow-sm">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <span>Upload Program</span>
                        </button>
                    </div>

                    <div id="daftar-program-container" class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                        @forelse($programs ?? [] as $p)
                            @php /** @var \App\Models\ProgramLatihan $p */ @endphp
                            <div id="card-program-{{ $p->id }}" class="bg-white text-slate-800 rounded-3xl p-6 shadow-sm border border-slate-200 hover:border-sky-300 transition flex flex-col justify-between space-y-4">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between gap-2 flex-wrap">
                                        <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-sky-50 text-[#0275d8] border border-sky-200">
                                            {{ $p->kelompok }}
                                        </span>
                                        <span class="text-[11px] text-slate-500">
                                            <i class="fa-regular fa-calendar mr-1 text-sky-600"></i>{{ $p->tanggal }}
                                        </span>
                                    </div>

                                    @if($p->fase)
                                        <div class="inline-block px-3 py-1 rounded-lg bg-amber-50 border border-amber-200 text-amber-700 text-[10px] font-bold uppercase tracking-wider">
                                            {{ $p->fase }}
                                        </div>
                                    @endif

                                    <div>
                                        <h4 class="text-base sm:text-lg font-bold text-slate-900 leading-snug">{{ $p->judul_minggu }}</h4>
                                        @if($p->target_event)
                                            <p class="text-xs text-slate-500 mt-1">{{ $p->target_event }}</p>
                                        @endif
                                    </div>

                                    @if($p->alat)
                                        <p class="text-xs text-slate-600 leading-relaxed">
                                            <span class="text-slate-800 font-semibold">Alat klub dipakai:</span> {{ $p->alat }}
                                        </p>
                                    @endif
                                </div>

                                <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        @if($p->file_path)
                                            <a href="{{ $p->file_path }}" target="_blank"
                                                class="inline-flex items-center space-x-1.5 bg-sky-50 hover:bg-[#0275d8] text-sky-700 hover:text-white text-xs font-semibold px-3.5 py-2 rounded-xl border border-sky-200 transition">
                                                <i class="fa-solid fa-file-arrow-down"></i>
                                                <span>Unduh / Lihat File</span>
                                            </a>
                                        @endif
                                    </div>
                                    <button type="button" onclick="hapusProgram({{ $p->id }}, '{{ addslashes($p->judul_minggu) }}')"
                                        class="inline-flex items-center space-x-1 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-semibold px-3 py-2 rounded-xl border border-red-200 transition">
                                        <i class="fa-solid fa-trash-can"></i>
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div id="empty-program-card" class="lg:col-span-2 bg-white rounded-3xl p-8 text-center text-slate-400 text-xs border border-slate-200">
                                Belum ada program latihan yang diupload.
                            </div>
                        @endforelse
                    </div>
                </section>

                <!-- TAB 6: BEST TIME (KHUSUS CHAMPIONS) -->
                <section id="tab-best-time" class="dashboard-panel space-y-6 hidden">
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Catatan Best Time — Champions</h3>
                                <p class="text-xs text-slate-500">Rekam jejak waktu terbaik atlet kelompok Champions berdasarkan gaya dan jarak</p>
                            </div>
                            <button type="button" onclick="openModal('modal-best-time')"
                                class="inline-flex items-center justify-center space-x-2 bg-[#0275d8] hover:bg-sky-600 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition shadow-sm">
                                <i class="fa-solid fa-plus"></i>
                                <span>Input Best Time</span>
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse whitespace-nowrap">
                                <thead>
                                    <tr class="border-b border-slate-100 text-slate-400 uppercase font-semibold">
                                        <th class="pb-3 px-3">Nama Atlet</th>
                                        <th class="pb-3 px-3">Gaya</th>
                                        <th class="pb-3 px-3 text-center">Jarak</th>
                                        <th class="pb-3 px-3 text-center">Best Time</th>
                                        <th class="pb-3 px-3">Tanggal</th>
                                        <th class="pb-3 px-3">Keterangan</th>
                                        <th class="pb-3 px-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tabel-best-time-body" class="divide-y divide-slate-100 text-slate-700">
                                    @forelse($bestTimes ?? [] as $bt)
                                        @php /** @var \App\Models\BestTime $bt */ @endphp
                                        <tr id="row-best-time-{{ $bt->id }}">
                                            <td class="py-3 px-3 font-semibold text-slate-900">{{ $bt->nama_atlet }}</td>
                                            <td class="py-3 px-3">{{ $bt->gaya }}</td>
                                            <td class="py-3 px-3 text-center">{{ $bt->jarak }}</td>
                                            <td class="py-3 px-3 text-center">
                                                <span class="px-2.5 py-1 rounded-full text-[11px] font-mono font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    {{ $bt->waktu }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-3 text-slate-500">{{ $bt->tanggal }}</td>
                                            <td class="py-3 px-3 text-slate-600">{{ $bt->keterangan ?? '-' }}</td>
                                            <td class="py-3 px-3 text-center">
                                                <button type="button" onclick="hapusBestTime({{ $bt->id }}, '{{ addslashes($bt->nama_atlet) }}')"
                                                    class="bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 px-2.5 py-1 rounded-lg text-[11px] font-semibold transition">
                                                    Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr id="empty-best-time-row">
                                            <td colspan="7" class="py-6 text-center text-slate-400">Belum ada catatan Best Time atlet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            @endif
        </main>
    </div>

    <!-- MODAL 1: INPUT PRESENSI MANUAL -->
    <div id="modal-kehadiran" onclick="if(event.target === this) closeModal('modal-kehadiran')" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-slate-200 relative">
            <button type="button" onclick="closeModal('modal-kehadiran')" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
            <h3 class="text-base font-bold text-slate-900 mb-1">Presensi Manual Atlet</h3>
            <p class="text-xs text-slate-500 mb-4">Catat kehadiran, telat, izin, atau sakit atlet</p>

            <form id="form-kehadiran" onsubmit="handleManualAbsenPelatihMurid(event)" class="space-y-3 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Atlet</label>
                    <select id="pelatih_manual_nama" required class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                        @forelse($athletes ?? [] as $a)
                            <option value="{{ $a->name }}">{{ $a->name }}</option>
                        @empty
                            <option value="" disabled selected>Belum ada atlet di kelompok ini</option>
                        @endforelse
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Sesi Latihan</label>
                    <select id="pelatih_manual_sesi" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                        <option value="Sesi Sore (15:30 WIB)">Sesi Sore (15:30 WIB)</option>
                        <option value="Sesi Pagi (06:00 WIB)">Sesi Pagi (06:00 WIB)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Status Kehadiran</label>
                    <select id="pelatih_manual_status" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                        <option value="HADIR">Hadir</option>
                        <option value="TELAT">Telat</option>
                        <option value="IZIN">Izin</option>
                        <option value="SAKIT">Sakit</option>
                        <option value="ALFA">Alfa</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeModal('modal-kehadiran')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-4 py-2 rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="bg-[#0275d8] hover:bg-sky-600 text-white font-semibold px-5 py-2 rounded-xl transition">
                        Simpan Presensi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: INPUT / EDIT DATA ATLET -->
    <div id="modal-data-atlet" onclick="if(event.target === this) closeModal('modal-data-atlet')" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-3xl p-6 max-w-2xl w-full shadow-2xl border border-slate-200 relative my-8">
            <button type="button" onclick="closeModal('modal-data-atlet')" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
            <h3 id="modal-data-atlet-title" class="text-base font-bold text-slate-900 mb-1">Input Data Atlet ({{ $coachKelompok }})</h3>
            <p class="text-xs text-slate-500 mb-4">Lengkapi biodata, data fisik, serta sekolah dan kelas atlet</p>

            <form id="form-data-atlet" onsubmit="handleSimpanDataAtlet(event)" class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 text-xs">
                <input type="hidden" id="data_atlet_id" value="">

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama</label>
                    <input type="text" id="data_atlet_nama" required placeholder="Nama lengkap atlet"
                        class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">NIK</label>
                    <input type="text" id="data_atlet_nik" required placeholder="16 digit NIK"
                        class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">NISNAS</label>
                    <input type="text" id="data_atlet_nisnas" required placeholder="Nomor NISNAS"
                        class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tempat Tanggal Lahir</label>
                    <input type="text" id="data_atlet_ttl" required placeholder="Contoh: Sleman, 14-05-2012"
                        class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">No HP</label>
                    <input type="text" id="data_atlet_hp" required placeholder="08xxxxxxxxxx"
                        class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Gmail</label>
                    <input type="email" id="data_atlet_gmail" required placeholder="nama@gmail.com"
                        class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tinggi Badan (cm)</label>
                    <input type="number" step="0.1" id="data_atlet_tinggi" required placeholder="Contoh: 158"
                        class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Berat Badan (kg)</label>
                    <input type="number" step="0.1" id="data_atlet_berat" required placeholder="Contoh: 46"
                        class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Sekolah</label>
                    <input type="text" id="data_atlet_sekolah" required placeholder="Nama sekolah"
                        class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Kelas</label>
                    <input type="text" id="data_atlet_kelas" required placeholder="Contoh: VIII B"
                        class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                </div>

                <div class="sm:col-span-2 flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeModal('modal-data-atlet')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-4 py-2 rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" id="btn-simpan-data-atlet" class="bg-[#0275d8] hover:bg-sky-600 text-white font-semibold px-5 py-2 rounded-xl transition">
                        Simpan Data Atlet
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($isChampions)
        <!-- MODAL 3: TAMBAH EVENT (KHUSUS PELATIH CHAMPIONS) -->
        <div id="modal-event" onclick="if(event.target === this) closeModal('modal-event')" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white rounded-3xl p-6 max-w-lg w-full shadow-2xl border border-slate-200 relative my-8">
                <button type="button" onclick="closeModal('modal-event')" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
                <h3 class="text-base font-bold text-slate-900 mb-1">Tambah Event Kejuaraan</h3>
                <p class="text-xs text-slate-500 mb-4">Jadwal event akan tampil di dashboard seluruh pelatih dan admin</p>

                <form id="form-event" onsubmit="handleSimpanEvent(event)" class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 text-xs">
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-slate-700 mb-1">Nama Event</label>
                        <input type="text" id="ev_nama" required placeholder="Contoh: IOAC (Indonesia Open Aquatic Championship) 2026 – Renang"
                            class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tingkat</label>
                        <select id="ev_tingkat" required class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                            <option value="Kabupaten/Kota">Kabupaten/Kota</option>
                            <option value="Provinsi">Provinsi</option>
                            <option value="Nasional">Nasional</option>
                            <option value="Internasional">Internasional</option>
                            <option value="Klub / Internal">Klub / Internal</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Lokasi</label>
                        <input type="text" id="ev_lokasi" required placeholder="Contoh: Stadion Akuatik GBK, Jakarta"
                            class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tanggal Mulai</label>
                        <input type="date" id="ev_tgl_mulai" required value="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tanggal Selesai (Opsional)</label>
                        <input type="date" id="ev_tgl_selesai"
                            class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                    </div>

                    <div class="sm:col-span-2 flex justify-end gap-2 pt-2">
                        <button type="button" onclick="closeModal('modal-event')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-4 py-2 rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" class="bg-[#0275d8] hover:bg-sky-600 text-white font-semibold px-5 py-2 rounded-xl transition">
                            Simpan Event
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL 4: UPLOAD PROGRAM LATIHAN -->
        <div id="modal-program" onclick="if(event.target === this) closeModal('modal-program')" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white rounded-3xl p-6 max-w-2xl w-full shadow-2xl border border-slate-200 relative my-8">
                <button type="button" onclick="closeModal('modal-program')" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
                <h3 class="text-base font-bold text-slate-900 mb-1">Upload Program Latihan (Champions)</h3>
                <p class="text-xs text-slate-500 mb-4">Unggah file program beserta keterangan lintasan, target lomba, dan alat</p>

                <form id="form-program-latihan" onsubmit="handleSimpanProgram(event)" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Kelompok / Lintasan</label>
                        <input type="text" id="prog_kelompok" required value="Champions" placeholder="Contoh: Champions - Lintasan 6"
                            class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tanggal Mulai Minggu</label>
                        <input type="date" id="prog_tanggal" required value="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-slate-700 mb-1">Fase / Kategori Badge</label>
                        <input type="text" id="prog_fase" placeholder="Contoh: KU-III · PRA-KOMPETISI · MINGGU PEMULIHAN"
                            class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-slate-700 mb-1">Judul Minggu & Total Jarak/Sesi</label>
                        <input type="text" id="prog_judul" required placeholder="Contoh: Minggu 17: 21.100 m dalam 5 sesi"
                            class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Target Event / Lomba</label>
                        <input type="text" id="prog_target_event" placeholder="Contoh: Menuju Kadisdik Sumedang (20 Des 26)"
                            class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Alat Klub Dipakai</label>
                        <input type="text" id="prog_alat" placeholder="Contoh: papan, pull buoy, fin, snorkel, paddle"
                            class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-slate-700 mb-1">Upload File Program (PDF / Excel / Word / Gambar, maks 10MB)</label>
                        <input type="file" id="prog_file" required accept=".pdf,.xls,.xlsx,.doc,.docx,.jpg,.jpeg,.png"
                            class="w-full px-3 py-2 border border-dashed border-slate-300 rounded-xl bg-slate-50 text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-sky-100 file:text-sky-700 hover:file:bg-sky-200">
                    </div>

                    <div class="sm:col-span-2 flex justify-end gap-2 pt-2">
                        <button type="button" onclick="closeModal('modal-program')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-4 py-2 rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" id="btn-simpan-program" class="bg-[#0275d8] hover:bg-sky-600 text-white font-semibold px-5 py-2 rounded-xl transition">
                            Upload & Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL 5: INPUT BEST TIME -->
        <div id="modal-best-time" onclick="if(event.target === this) closeModal('modal-best-time')" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white rounded-3xl p-6 max-w-lg w-full shadow-2xl border border-slate-200 relative my-8">
                <button type="button" onclick="closeModal('modal-best-time')" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
                <h3 class="text-base font-bold text-slate-900 mb-1">Catat Best Time Atlet (Champions)</h3>
                <p class="text-xs text-slate-500 mb-4">Rekam catatan waktu terbaik atlet berdasarkan gaya dan jarak</p>

                <form id="form-best-time" onsubmit="handleSimpanBestTime(event)" class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 text-xs">
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-slate-700 mb-1">Nama Atlet</label>
                        <select id="bt_nama_atlet" required class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                            @forelse($athletes ?? [] as $a)
                                <option value="{{ $a->name }}">{{ $a->name }}</option>
                            @empty
                                <option value="" disabled selected>Belum ada atlet di kelompok ini</option>
                            @endforelse
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Gaya Renang</label>
                        <select id="bt_gaya" required class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                            <option value="Gaya Bebas">Gaya Bebas</option>
                            <option value="Gaya Dada">Gaya Dada</option>
                            <option value="Gaya Punggung">Gaya Punggung</option>
                            <option value="Gaya Kupu-kupu">Gaya Kupu-kupu</option>
                            <option value="Gaya Ganti (IM)">Gaya Ganti (IM)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Jarak</label>
                        <select id="bt_jarak" required class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                            <option value="25m">25m</option>
                            <option value="50m" selected>50m</option>
                            <option value="100m">100m</option>
                            <option value="200m">200m</option>
                            <option value="400m">400m</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Waktu (MM:SS.ms)</label>
                        <input type="text" id="bt_waktu" required placeholder="Contoh: 00:31.45"
                            class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 font-mono">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Tanggal</label>
                        <input type="date" id="bt_tanggal" required value="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-slate-700 mb-1">Keterangan</label>
                        <input type="text" id="bt_keterangan" placeholder="Latihan / Kejurda"
                            class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                    </div>

                    <div class="sm:col-span-2 flex justify-end gap-2 pt-2">
                        <button type="button" onclick="closeModal('modal-best-time')" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-4 py-2 rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" class="bg-[#0275d8] hover:bg-sky-600 text-white font-semibold px-5 py-2 rounded-xl transition">
                            Simpan Best Time
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- JavaScript Terpisah -->
    <script src="/js/pelatih-dashboard.js"></script>
</body>
</html>
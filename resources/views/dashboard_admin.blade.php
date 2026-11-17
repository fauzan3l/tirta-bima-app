<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tirta Bima — Dashboard Admin</title>
    
    <!-- Tailwind CSS & Font Awesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS Terpisah -->
    <link rel="stylesheet" href="/css/admin-dashboard.css">
    
    <!-- Library QR Scanner -->
    <script src="https://unpkg.com/html5-qrcode"></script>
</head>
<body class="bg-slate-100 min-h-screen font-sans antialiased text-slate-800 flex flex-col md:flex-row">

    <!-- SIDEBAR ADMIN -->
    <aside class="w-full md:w-72 bg-[#0b2447] text-white flex flex-col justify-between shrink-0 md:min-h-screen shadow-xl z-20">
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
                    <div class="w-10 h-10 rounded-full bg-sky-500/20 border border-sky-400 flex items-center justify-center text-sky-300 font-bold text-sm">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-xs font-semibold text-white truncate">{{ Auth::user()->name ?? 'Administrator' }}</p>
                        <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wide bg-amber-400/20 text-amber-300 border border-amber-400/30">
                            Administrator
                        </span>
                    </div>
                </div>
            </div>

            <!-- Menu Sidebar -->
            <nav class="p-4 space-y-1.5 text-xs font-medium">
                <button onclick="switchTab('beranda')" id="btn-beranda" class="nav-tab w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition bg-[#0275d8] text-white">
                    <i class="fa-solid fa-table-columns text-sm w-5 text-center"></i>
                    <span>Beranda Dashboard</span>
                </button>

                <button onclick="switchTab('kehadiran-atlet')" id="btn-kehadiran-atlet" class="nav-tab w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition text-slate-300 hover:bg-white/10 hover:text-white">
                    <i class="fa-solid fa-clipboard-user text-sm w-5 text-center"></i>
                    <span>Kehadiran Atlet</span>
                </button>

                <button onclick="switchTab('kehadiran-pelatih')" id="btn-kehadiran-pelatih" class="nav-tab w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition text-slate-300 hover:bg-white/10 hover:text-white">
                    <i class="fa-solid fa-user-tie text-sm w-5 text-center"></i>
                    <span>Kehadiran Pelatih</span>
                </button>

                <button onclick="switchTab('scanner-kamera')" id="btn-scanner-kamera" class="nav-tab w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition text-slate-300 hover:bg-white/10 hover:text-white">
                    <i class="fa-solid fa-qrcode text-sm w-5 text-center"></i>
                    <span>Scan Absensi (Kamera)</span>
                </button>

                <button onclick="switchTab('kelola-akun')" id="btn-kelola-akun" class="nav-tab w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition text-slate-300 hover:bg-white/10 hover:text-white">
                    <i class="fa-solid fa-users-gear text-sm w-5 text-center"></i>
                    <span>Kelola Akun & Barcode</span>
                </button>
            </nav>
        </div>

        <div class="p-4 border-t border-slate-700/60">
            <div class="text-[10px] text-slate-400 mb-3 px-2 flex justify-between items-center">
                <span>SEJAK 2018</span>
                <span class="text-sky-400 font-semibold">Tirta System</span>
            </div>
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
    <div class="flex-1 flex flex-col min-h-screen overflow-y-auto">
        <header class="bg-white border-b border-slate-200 px-6 lg:px-8 py-4 flex items-center justify-between sticky top-0 z-10">
            <div>
                <h1 id="page-title" class="text-lg font-bold text-slate-900">Beranda Dashboard</h1>
                <p id="page-subtitle" class="text-xs text-slate-500">Pusat kontrol dan operasional klub renang</p>
            </div>
            <div class="flex items-center space-x-3 text-xs text-slate-500 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200">
                <i class="fa-regular fa-calendar-check text-sky-600"></i>
                <span class="font-medium">{{ date('d F Y') }}</span>
            </div>
        </header>

        <main class="p-6 lg:p-8 flex-1 space-y-6">

            <!-- TAB 1: BERANDA -->
            <section id="tab-beranda" class="space-y-6">
                <div class="bg-[#0b2447] text-white rounded-3xl p-8 relative overflow-hidden shadow-lg">
                    <div class="relative z-10 max-w-2xl">
                        <span class="text-xs font-bold uppercase tracking-widest text-sky-400">Portal Administrator</span>
                        <h2 class="text-2xl sm:text-3xl font-extrabold mt-1">Selamat Datang, {{ Auth::user()->name ?? 'Administrator' }}!</h2>
                        <p class="text-slate-300 text-xs sm:text-sm mt-2 leading-relaxed">
                            Kelola pendaftaran akun atlet, cetak barcode QR, serta jalankan scanner kamera absensi latihan sore (mulai 15:30 WIB).
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-[11px] font-semibold text-slate-500 uppercase">Total Atlet</p>
                                <h3 id="stat-total-atlet" class="text-2xl font-bold text-slate-900 mt-1">{{ ($athletes ?? collect())->count() }} Atlet</h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                                <i class="fa-solid fa-users"></i>
                            </div>
                        </div>
                        <p class="mt-3 text-[11px] text-slate-400">Terdaftar di klub</p>
                    </div>

                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-[11px] font-semibold text-slate-500 uppercase">Hadir Hari Ini</p>
                                <h3 id="stat-hadir-hari-ini" class="text-2xl font-bold text-emerald-600 mt-1">{{ $hadirHariIni ?? 0 }} Sesi</h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <i class="fa-solid fa-clipboard-check"></i>
                            </div>
                        </div>
                        <p class="mt-3 text-[11px] text-emerald-600 font-medium">Batas tepat waktu: 15:30 WIB</p>
                    </div>

                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-[11px] font-semibold text-slate-500 uppercase">Total Pelatih</p>
                                <h3 id="stat-total-pelatih" class="text-2xl font-bold text-slate-900 mt-1">{{ ($coaches ?? collect())->count() }} Orang</h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                                <i class="fa-solid fa-user-tie"></i>
                            </div>
                        </div>
                        <p class="mt-3 text-[11px] text-slate-400">Instruktur resmi klub</p>
                    </div>

                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-[11px] font-semibold text-slate-500 uppercase">Kondisi Kolam</p>
                                <h3 class="text-2xl font-bold text-sky-600 mt-1">Optimal</h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                                <i class="fa-solid fa-water"></i>
                            </div>
                        </div>
                        <p class="mt-3 text-[11px] text-slate-400">pH 7.3 | Suhu 27°C</p>
                    </div>
                </div>
            </section>

            <!-- TAB 2: KEHADIRAN ATLET -->
            <section id="tab-kehadiran-atlet" class="space-y-6 hidden">
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
                    <h3 class="text-base font-bold text-slate-900 mb-1">Form Absensi Manual Atlet</h3>
                    <p class="text-xs text-slate-500 mb-4">Gunakan scanner kamera untuk scan otomatis atau gunakan form ini</p>

                    <form onsubmit="handleManualAbsen(event)" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Pilih Atlet</label>
                            <select id="manual_nama" required class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                                <option value="">-- Pilih Atlet --</option>
                                @foreach($athletes ?? [] as $athlete)
                                    <option value="{{ $athlete->name }}">{{ $athlete->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Sesi Latihan</label>
                            <select id="manual_sesi" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                                <option value="Sesi Sore (15:30 WIB)">Sesi Sore (15:30 WIB)</option>
                                <option value="Sesi Pagi (06:00 WIB)">Sesi Pagi (06:00 WIB)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Status Kehadiran</label>
                            <select id="manual_status" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                                <option value="HADIR">Hadir</option>
                                <option value="TELAT">Telat</option>
                                <option value="IZIN">Izin</option>
                                <option value="SAKIT">Sakit</option>
                                <option value="ALFA">Alfa</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-[#0275d8] hover:bg-sky-600 text-white font-semibold py-2.5 px-4 rounded-xl transition">
                                Simpan Presensi
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                    <h3 class="text-base font-bold text-slate-900 mb-3">Catatan Kehadiran Atlet Hari Ini</h3>
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
                            <tbody id="tabel-presensi-body" class="divide-y divide-slate-100 text-slate-700">
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
                                        <td class="py-3 px-3 text-center"><span class="px-2.5 py-1 rounded-full text-[10px] font-semibold border {{ $badgeClass }}">{{ $st }}</span></td>
                                    </tr>
                                @empty
                                    <tr id="empty-atlet-row">
                                        <td colspan="4" class="py-6 text-center text-slate-400">Belum ada atlet yang melakukan presensi hari ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- TAB 3: KEHADIRAN PELATIH -->
            <section id="tab-kehadiran-pelatih" class="space-y-6 hidden">
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
                    <h3 class="text-base font-bold text-slate-900 mb-1">Form Absensi Manual Pelatih</h3>
                    <p class="text-xs text-slate-500 mb-4">Gunakan scanner kamera untuk scan otomatis atau gunakan form ini</p>

                    <form onsubmit="handleManualAbsenPelatih(event)" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Pilih Pelatih</label>
                            <select id="manual_pelatih_nama" required class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                                <option value="">-- Pilih Pelatih --</option>
                                @foreach($coaches ?? [] as $coach)
                                    <option value="{{ $coach->name }}">{{ $coach->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Sesi Latihan</label>
                            <select id="manual_pelatih_sesi" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                                <option value="Sesi Sore (15:30 WIB)">Sesi Sore (15:30 WIB)</option>
                                <option value="Sesi Pagi (06:00 WIB)">Sesi Pagi (06:00 WIB)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Status Kehadiran</label>
                            <select id="manual_pelatih_status" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                                <option value="HADIR">Hadir</option>
                                <option value="TELAT">Telat</option>
                                <option value="IZIN">Izin</option>
                                <option value="SAKIT">Sakit</option>
                                <option value="ALFA">Alfa</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-[#0275d8] hover:bg-sky-600 text-white font-semibold py-2.5 px-4 rounded-xl transition">
                                Simpan Presensi
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                    <h3 class="text-base font-bold text-slate-900 mb-3">Catatan Kehadiran Pelatih Hari Ini</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-400 uppercase font-semibold">
                                    <th class="pb-3 px-3">Nama Pelatih</th>
                                    <th class="pb-3 px-3">Jam Masuk</th>
                                    <th class="pb-3 px-3">Sesi</th>
                                    <th class="pb-3 px-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody id="tabel-presensi-pelatih-body" class="divide-y divide-slate-100 text-slate-700">
                                @forelse($coachAttendances ?? [] as $att)
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
                                        <td class="py-3 px-3 text-center"><span class="px-2.5 py-1 rounded-full text-[10px] font-semibold border {{ $badgeClass }}">{{ $st }}</span></td>
                                    </tr>
                                @empty
                                    <tr id="empty-pelatih-row">
                                        <td colspan="4" class="py-6 text-center text-slate-400">Belum ada pelatih yang melakukan presensi hari ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- TAB 4: SCANNER KAMERA -->
            <section id="tab-scanner-kamera" class="space-y-6 hidden">
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-6">
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Pemindai Barcode / QR Code Presensi</h3>
                            <p class="text-xs text-slate-500">Arahkan QR Code atlet ke kamera. Latihan sore dimulai: <strong class="text-sky-600 font-semibold">15:30 WIB</strong> (setelah itu status terhitung TELAT).</p>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="startScanner()" class="bg-[#0275d8] hover:bg-sky-600 text-white text-xs px-4 py-2 rounded-xl flex items-center space-x-1 font-semibold transition">
                                <i class="fa-solid fa-camera"></i>
                                <span>Buka Kamera</span>
                            </button>
                            <button onclick="stopScanner()" class="bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs px-4 py-2 rounded-xl font-medium transition">
                                Tutup Kamera
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                        <div class="bg-slate-50 border-2 border-dashed border-slate-300 rounded-2xl p-4 flex flex-col items-center justify-center min-h-[300px]">
                            <div id="reader" class="w-full max-w-sm rounded-xl overflow-hidden shadow-inner"></div>
                            <span class="text-xs text-slate-400 mt-2"><i class="fa-solid fa-video mr-1"></i> Arahkan QR Code ke kotak kamera</span>
                        </div>

                        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5">
                            <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">Hasil Pemindaian Terakhir</h4>
                            <div id="scan-result-card" class="hidden space-y-3">
                                <div class="p-4 rounded-xl border" id="result-box">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h5 id="res-name" class="text-base font-bold text-slate-900">-</h5>
                                            <p id="res-token" class="text-xs font-mono text-slate-500">-</p>
                                        </div>
                                        <span id="res-status-badge" class="px-2.5 py-1 rounded-full text-xs font-bold uppercase">-</span>
                                    </div>
                                    <div class="mt-3 pt-3 border-t border-slate-200/60 text-xs text-slate-600 space-y-1">
                                        <p><strong>Waktu Presensi:</strong> <span id="res-time">-</span></p>
                                        <p><strong>Keterangan:</strong> <span id="res-notes">-</span></p>
                                    </div>
                                </div>
                            </div>
                            <div id="scan-placeholder" class="py-12 text-center text-slate-400 text-xs">
                                <i class="fa-solid fa-qrcode text-4xl mb-2 text-slate-300 block"></i>
                                Kamera siap memindai QR Code.
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- TAB 5: KELOLA AKUN -->
            <section id="tab-kelola-akun" class="space-y-6 hidden">
                @if(session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs flex items-center space-x-2">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                        <span class="font-semibold">{{ session('success') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-2xl text-xs space-y-1">
                        <p class="font-bold">Gagal mendaftarkan akun:</p>
                        <ul class="list-disc list-inside text-[11px]">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
                    <h3 class="text-base font-bold text-slate-900 mb-1">Daftarkan Akun Anggota Baru</h3>
                    <p class="text-xs text-slate-500 mb-4">Sistem otomatis membuatkan token Barcode QR dan menyimpannya ke database.</p>

                    <form action="{{ route('admin.storeUser') }}" method="POST"
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
                        @csrf

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                placeholder="Nama anggota"
                                class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                placeholder="email@tirta.com"
                                class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">No. WhatsApp</label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                placeholder="08xxxxxxxxxx"
                                class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Role</label>
                            <select name="role" required
                                class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                                <option value="murid" {{ old('role') === 'murid' ? 'selected' : '' }}>Atlet (Murid)</option>
                                <option value="pelatih" {{ old('role') === 'pelatih' ? 'selected' : '' }}>Pelatih (Coach)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Password</label>
                            <input type="password" name="password" required minlength="6"
                                placeholder="Min 6 karakter"
                                class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                        </div>

                        <div class="sm:col-span-2 lg:col-span-5 flex justify-end mt-2">
                            <button type="submit"
                                class="bg-[#0275d8] hover:bg-sky-600 text-white font-semibold px-5 py-2.5 rounded-xl transition">
                                Simpan & Terbitkan QR Code
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                    <h3 class="text-base font-bold text-slate-900 mb-3">Daftar Akun & Barcode Absensi</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-400 uppercase font-semibold">
                                    <th class="pb-3 px-3">Nama</th>
                                    <th class="pb-3 px-3">Role</th>
                                    <th class="pb-3 px-3">Kode Token</th>
                                    <th class="pb-3 px-3">No. HP</th>
                                    <th class="pb-3 px-3 text-center">Aksi QR</th>
                                </tr>
                            </thead>
                            <tbody id="tabel-akun-body" class="divide-y divide-slate-100 text-slate-700">
                                @forelse(($athletes ?? collect())->concat($coaches ?? collect()) as $u)
                                    <tr>
                                        <td class="py-3 px-3 font-semibold text-slate-900">{{ $u->name }}</td>
                                        <td class="py-3 px-3"><span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-sky-50 text-sky-700 border border-sky-200">{{ $u->role }}</span></td>
                                        <td class="py-3 px-3 font-mono text-slate-500">{{ $u->barcode_token ?? ('TB-'.strtoupper(substr($u->role, 0, 1)).'-'.$u->id) }}</td>
                                        <td class="py-3 px-3 text-slate-500">{{ $u->phone ?? '-' }}</td>
                                        <td class="py-3 px-3 text-center">
                                            <button onclick="showQrModal('{{ addslashes($u->name) }}', '{{ $u->role }}', '{{ $u->barcode_token ?? ('TB-'.strtoupper(substr($u->role, 0, 1)).'-'.$u->id) }}')" 
                                                class="bg-slate-100 hover:bg-sky-50 text-sky-600 border border-slate-200 px-3 py-1.5 rounded-lg text-xs font-semibold">
                                                Lihat QR
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-6 text-center text-slate-400">Belum ada akun anggota terdaftar.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- MODAL QR -->
    <div id="qrModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
        <div class="bg-white rounded-3xl p-6 max-w-sm w-full text-center shadow-2xl relative border border-slate-200">
            <button onclick="closeQrModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            <span class="text-[10px] font-bold uppercase tracking-widest text-sky-600">Kartu Presensi Tirta Bima</span>
            <h4 id="modalName" class="text-lg font-bold text-slate-900 mt-1">-</h4>
            <p id="modalRole" class="text-xs text-slate-500 mb-4 font-semibold uppercase tracking-wider">-</p>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 inline-block mb-3">
                <img id="modalQrImg" src="" alt="QR Code" class="w-48 h-48 mx-auto rounded-lg">
            </div>
            <p id="modalToken" class="text-xs font-mono text-slate-400 mb-4 tracking-wider">-</p>
            <button onclick="window.print()" class="w-full bg-[#0275d8] hover:bg-sky-600 text-white font-medium py-2 rounded-xl text-xs flex items-center justify-center space-x-2 transition">
                <i class="fa-solid fa-print"></i><span>Cetak Kartu QR</span>
            </button>
        </div>
    </div>

    <!-- JavaScript Terpisah -->
    <script src="/js/admin-dashboard.js"></script>
</body>
</html>
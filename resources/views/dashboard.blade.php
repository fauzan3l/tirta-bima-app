<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tirta Bima — Dashboard {{ ucfirst(Auth::user()->role ?? 'Member') }}</title>
    <!-- Tailwind CSS & Font Awesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Scanner QR Code HTML5-QRCode -->
    <script src="https://unpkg.com/html5-qrcode"></script>
</head>
<body class="bg-slate-100 min-h-screen font-sans antialiased text-slate-800 flex flex-col md:flex-row">

    <!-- ========================================== -->
    <!-- SIDEBAR KIRI                               -->
    <!-- ========================================== -->
    <aside class="w-full md:w-72 bg-[#0b2447] text-white flex flex-col justify-between shrink-0 md:min-h-screen shadow-xl z-20">
        <div>
            <!-- Branding -->
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

            <!-- Profil User & Role Badge -->
            <div class="px-6 py-5 border-b border-slate-700/40 bg-white/5">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-sky-500/20 border border-sky-400 flex items-center justify-center text-sky-300 font-bold text-sm">
                        {{ strtoupper(substr(Auth::user()->name ?? 'T', 0, 1)) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-xs font-semibold text-white truncate">{{ Auth::user()->name ?? 'Member Tirta' }}</p>
                        <div class="flex items-center space-x-2 mt-0.5">
                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wide
                                {{ (Auth::user()->role ?? 'murid') === 'admin' ? 'bg-amber-400/20 text-amber-300 border border-amber-400/30' : '' }}
                                {{ (Auth::user()->role ?? 'murid') === 'pelatih' ? 'bg-sky-400/20 text-sky-300 border border-sky-400/30' : '' }}
                                {{ (Auth::user()->role ?? 'murid') === 'murid' ? 'bg-emerald-400/20 text-emerald-300 border border-emerald-400/30' : '' }}">
                                {{ Auth::user()->role ?? 'murid' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Menu Sidebar -->
            <nav class="p-4 space-y-1.5 text-xs font-medium">
                <!-- Menu 1: Beranda -->
                <button onclick="switchTab('beranda')" id="btn-beranda" 
                    class="nav-tab w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition duration-150 bg-[#0275d8] text-white">
                    <i class="fa-solid fa-table-columns text-sm w-5 text-center"></i>
                    <span>Beranda Dashboard</span>
                </button>

                <!-- Menu 2: Kehadiran Atlet -->
                <button onclick="switchTab('kehadiran-atlet')" id="btn-kehadiran-atlet" 
                    class="nav-tab w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition duration-150 text-slate-300 hover:bg-white/10 hover:text-white">
                    <i class="fa-solid fa-clipboard-user text-sm w-5 text-center"></i>
                    <span>
                        @if((Auth::user()->role ?? 'murid') === 'murid')
                            Riwayat Kehadiran Saya
                        @else
                            Kehadiran Atlet
                        @endif
                    </span>
                </button>

                <!-- Menu Khusus Admin -->
                @if((Auth::user()->role ?? 'murid') === 'admin')
                    <!-- Menu 3: Kehadiran Pelatih -->
                    <button onclick="switchTab('kehadiran-pelatih')" id="btn-kehadiran-pelatih" 
                        class="nav-tab w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition duration-150 text-slate-300 hover:bg-white/10 hover:text-white">
                        <i class="fa-solid fa-user-tie text-sm w-5 text-center"></i>
                        <span>Kehadiran Pelatih</span>
                    </button>

                    <!-- Menu 4: Scanner Kamera Barcode -->
                    <button onclick="switchTab('scanner-kamera')" id="btn-scanner-kamera" 
                        class="nav-tab w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition duration-150 text-slate-300 hover:bg-white/10 hover:text-white">
                        <i class="fa-solid fa-qrcode text-sm w-5 text-center"></i>
                        <span>Scan Absensi (Kamera)</span>
                    </button>

                    <!-- Menu 5: Kelola Akun & Barcode -->
                    <button onclick="switchTab('kelola-akun')" id="btn-kelola-akun" 
                        class="nav-tab w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition duration-150 text-slate-300 hover:bg-white/10 hover:text-white">
                        <i class="fa-solid fa-users-gear text-sm w-5 text-center"></i>
                        <span>Kelola Akun & Barcode</span>
                    </button>
                @endif
            </nav>
        </div>

        <!-- Sidebar Bawah & Tombol Logout -->
        <div class="p-4 border-t border-slate-700/60">
            <div class="text-[10px] text-slate-400 mb-3 px-2 flex justify-between items-center">
                <span>SEJAK 2018</span>
                <span class="text-sky-400 font-semibold">Tirta System</span>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-white/10 hover:bg-red-600/90 text-white px-4 py-2.5 rounded-xl text-xs font-medium transition duration-150 flex items-center justify-center space-x-2 border border-white/10">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    <span>Keluar Akun</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- ========================================== -->
    <!-- KONTEN UTAMA                               -->
    <!-- ========================================== -->
    <div class="flex-1 flex flex-col min-h-screen overflow-y-auto">
        
        <!-- Header Atas -->
        <header class="bg-white border-b border-slate-200 px-6 lg:px-8 py-4 flex items-center justify-between sticky top-0 z-10">
            <div>
                <h1 id="page-title" class="text-lg font-bold text-slate-900">Beranda Dashboard</h1>
                <p id="page-subtitle" class="text-xs text-slate-500">
                    {{ (Auth::user()->role ?? 'murid') === 'murid' ? 'Ringkasan progres latihan renang mandiri' : 'Pusat kontrol dan operasional klub renang' }}
                </p>
            </div>
            <div class="flex items-center space-x-3 text-xs text-slate-500 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200">
                <i class="fa-regular fa-calendar-check text-sky-600"></i>
                <span class="font-medium">{{ date('d F Y') }}</span>
            </div>
        </header>

        <!-- Area Isi Halaman -->
        <main class="p-6 lg:p-8 flex-1 space-y-6">

            <!-- TAB 1: BERANDA DASHBOARD -->
            <section id="tab-beranda" class="space-y-6">
                <!-- Welcome Banner -->
                <div class="bg-[#0b2447] text-white rounded-3xl p-8 relative overflow-hidden shadow-lg">
                    <div class="relative z-10 max-w-2xl">
                        <span class="text-xs font-bold uppercase tracking-widest text-sky-400">Portal {{ ucfirst(Auth::user()->role ?? 'Member') }}</span>
                        <h2 class="text-2xl sm:text-3xl font-extrabold mt-1">Selamat Datang, {{ Auth::user()->name ?? 'Member Tirta' }}!</h2>
                        <p class="text-slate-300 text-xs sm:text-sm mt-2 leading-relaxed">
                            @if((Auth::user()->role ?? 'murid') === 'murid')
                                Jaga kedisiplinan dan ritme latihanmu. Latihan sesi sore dimulai tepat pukul <strong>15:30 WIB</strong>.
                            @elseif((Auth::user()->role ?? 'murid') === 'pelatih')
                                Atur dan pantau kehadiran atlet bimbinganmu pada sesi latihan hari ini.
                            @else
                                Kelola pendaftaran akun atlet, cetak barcode QR, serta jalankan scanner kamera absensi latihan sore.
                            @endif
                        </p>
                    </div>
                    <!-- Ilustrasi Kolam Mini -->
                    <div class="absolute -right-8 -bottom-8 opacity-20 pointer-events-none">
                        <div class="w-64 h-36 rounded-full border-4 border-sky-400 bg-sky-600/30 flex items-center justify-center">
                            <i class="fa-solid fa-person-swimming text-4xl text-sky-200"></i>
                        </div>
                    </div>
                </div>

                <!-- Metric Cards -->
                @if((Auth::user()->role ?? 'murid') === 'murid')
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-[11px] font-semibold text-slate-500 uppercase">Total Hadir</p>
                                    <h3 class="text-2xl font-bold text-emerald-600 mt-1">14 Sesi</h3>
                                </div>
                                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                    <i class="fa-solid fa-user-check"></i>
                                </div>
                            </div>
                            <p class="mt-3 text-[11px] text-slate-400">Kehadiran fisik di kolam renang</p>
                        </div>

                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-[11px] font-semibold text-slate-500 uppercase">Izin / Sakit</p>
                                    <h3 class="text-2xl font-bold text-amber-600 mt-1">2 Kali</h3>
                                </div>
                                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                                    <i class="fa-solid fa-envelope-open-text"></i>
                                </div>
                            </div>
                            <p class="mt-3 text-[11px] text-slate-400">Tercatat oleh pelatih</p>
                        </div>

                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-[11px] font-semibold text-slate-500 uppercase">Status Member</p>
                                    <h3 class="text-2xl font-bold text-sky-600 mt-1">Aktif</h3>
                                </div>
                                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                                    <i class="fa-solid fa-award"></i>
                                </div>
                            </div>
                            <p class="mt-3 text-[11px] text-slate-400">Tirta Bima Swimming Club</p>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-[11px] font-semibold text-slate-500 uppercase">Total Atlet</p>
                                    <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ count($athletes ?? []) > 0 ? count($athletes) : '48' }} Atlet</h3>
                                </div>
                                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                                    <i class="fa-solid fa-users"></i>
                                </div>
                            </div>
                            <p class="mt-3 text-[11px] text-slate-400">Terdaftar di database klub</p>
                        </div>

                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-[11px] font-semibold text-slate-500 uppercase">Hadir Hari Ini</p>
                                    <h3 class="text-2xl font-bold text-emerald-600 mt-1" id="stat-hadir-count">42 Sesi</h3>
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
                                    <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ count($coaches ?? []) > 0 ? count($coaches) : '6' }} Orang</h3>
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
                @endif

                <!-- Jadwal Sesi Latihan -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                    <h3 class="text-base font-bold text-slate-900 mb-4">Agenda Jadwal Latihan Renang</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-400 uppercase font-semibold">
                                    <th class="pb-3 px-2">Sesi / Jam</th>
                                    <th class="pb-3 px-2">Kategori</th>
                                    <th class="pb-3 px-2">Jalur Kolam</th>
                                    <th class="pb-3 px-2">Instruktur</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                <tr>
                                    <td class="py-3 px-2 font-semibold text-slate-900">06:00 - 08:00 WIB</td>
                                    <td class="py-3 px-2">Prestasi & Sprint</td>
                                    <td class="py-3 px-2">Lane 1 - 3</td>
                                    <td class="py-3 px-2">Coach Bima</td>
                                </tr>
                                <tr>
                                    <td class="py-3 px-2 font-semibold text-slate-900">15:30 - 17:30 WIB</td>
                                    <td class="py-3 px-2">Pemula & Perbaikan Gaya (Mulai 15:30)</td>
                                    <td class="py-3 px-2">Lane 4 - 6</td>
                                    <td class="py-3 px-2">Coach Rian</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- TAB 2: KEHADIRAN ATLET -->
            <section id="tab-kehadiran-atlet" class="space-y-6 hidden">
                @if((Auth::user()->role ?? 'murid') !== 'murid')
                    <!-- FORM INPUT MANUAL (ADMIN / PELATIH) -->
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Form Absensi Manual Atlet</h3>
                                <p class="text-xs text-slate-500">Gunakan scanner kamera untuk input otomatis atau form ini untuk izin/sakit</p>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-sky-600 bg-sky-50 px-3 py-1 rounded-full border border-sky-200">
                                Akses Pelatih/Admin
                            </span>
                        </div>

                        <form onsubmit="handleManualAbsen(event)" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Pilih Atlet</label>
                                <select id="manual_nama" required class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
                                    <option value="">-- Pilih Atlet Renang --</option>
                                    @forelse($athletes ?? [] as $athlete)
                                        <option value="{{ $athlete->name }}">{{ $athlete->name }}</option>
                                    @empty
                                        <option value="Dimas Anggara">Dimas Anggara</option>
                                        <option value="Nadia Lestari">Nadia Lestari</option>
                                        <option value="Rizky Pratama">Rizky Pratama</option>
                                    @endforelse
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Sesi Latihan</label>
                                <select id="manual_sesi" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
                                    <option value="Sesi Sore (15:30 WIB)">Sesi Sore (15:30 WIB)</option>
                                    <option value="Sesi Pagi (06:00 WIB)">Sesi Pagi (06:00 WIB)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Status Kehadiran</label>
                                <select id="manual_status" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
                                    <option value="HADIR">Hadir</option>
                                    <option value="TELAT">Telat</option>
                                    <option value="IZIN">Izin</option>
                                    <option value="SAKIT">Sakit</option>
                                    <option value="ALFA">Alfa</option>
                                </select>
                            </div>

                            <div class="flex items-end">
                                <button type="submit" class="w-full bg-[#0275d8] hover:bg-sky-600 text-white font-semibold py-2.5 px-4 rounded-xl transition duration-150 flex items-center justify-center space-x-2">
                                    <i class="fa-solid fa-check text-xs"></i>
                                    <span>Simpan Presensi</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Tabel Presensi Atlet Hari Ini -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                        <h3 class="text-base font-bold text-slate-900 mb-1">Catatan Kehadiran Atlet Hari Ini</h3>
                        <p class="text-xs text-slate-500 mb-4">Pembaruan realtime dari hasil scan barcode dan input manual</p>

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
                                    <tr>
                                        <td class="py-3 px-3 font-semibold text-slate-900">Dimas Anggara</td>
                                        <td class="py-3 px-3 text-slate-500">15:20 WIB</td>
                                        <td class="py-3 px-3">Sesi Sore (15:30 WIB)</td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">HADIR</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 px-3 font-semibold text-slate-900">Nadia Lestari</td>
                                        <td class="py-3 px-3 text-slate-500">15:25 WIB</td>
                                        <td class="py-3 px-3">Sesi Sore (15:30 WIB)</td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">HADIR</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 px-3 font-semibold text-slate-900">Rizky Pratama</td>
                                        <td class="py-3 px-3 text-slate-500">15:45 WIB</td>
                                        <td class="py-3 px-3">Sesi Sore (15:30 WIB)</td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-red-50 text-red-700 border border-red-200">TELAT (15 Mnt)</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <!-- UNTUK MURID (READ-ONLY + KARTU QR PRIBADI) -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Kartu QR Atlet -->
                        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200 text-center flex flex-col items-center justify-between">
                            <div>
                                <span class="text-[10px] font-bold text-sky-600 uppercase tracking-widest">Kartu Barcode Absensi</span>
                                <h4 class="text-base font-bold text-slate-900 mt-1">{{ Auth::user()->name }}</h4>
                                <p class="text-xs text-slate-400 mb-4 font-mono">{{ Auth::user()->barcode_token ?? 'TB-ATLET-'.Auth::id() }}</p>
                                
                                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 inline-block mb-3">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode(Auth::user()->barcode_token ?? 'TB-ATLET-'.Auth::id()) }}" 
                                         alt="QR Code Saya" class="w-44 h-44 mx-auto rounded-lg">
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                Tunjukkan QR ini ke kamera scanner sebelum pukul <strong>15:30 WIB</strong> agar tidak terhitung terlambat.
                            </p>
                        </div>

                        <!-- Riwayat Presensi -->
                        <div class="lg:col-span-2 bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-base font-bold text-slate-900">Riwayat Kehadiran Saya</h3>
                                    <p class="text-xs text-slate-500">Daftar rekapan sesi latihan yang telah Anda ikuti</p>
                                </div>
                                <span class="text-xs font-semibold text-sky-600 bg-sky-50 px-3 py-1 rounded-full border border-sky-200">
                                    Mode Siswa
                                </span>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs border-collapse">
                                    <thead>
                                        <tr class="border-b border-slate-100 text-slate-400 uppercase font-semibold">
                                            <th class="pb-3 px-3">Tanggal</th>
                                            <th class="pb-3 px-3">Waktu</th>
                                            <th class="pb-3 px-3">Sesi Latihan</th>
                                            <th class="pb-3 px-3 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 text-slate-700">
                                        <tr>
                                            <td class="py-3 px-3 font-semibold text-slate-900">28 Okt 2026</td>
                                            <td class="py-3 px-3 text-slate-500">15:20 WIB</td>
                                            <td class="py-3 px-3">Sesi Sore (15:30 - 17:30 WIB)</td>
                                            <td class="py-3 px-3 text-center">
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">HADIR</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-3 px-3 font-semibold text-slate-900">26 Okt 2026</td>
                                            <td class="py-3 px-3 text-slate-500">06:05 WIB</td>
                                            <td class="py-3 px-3">Sesi Pagi (06:00 - 08:00 WIB)</td>
                                            <td class="py-3 px-3 text-center">
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">HADIR</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-3 px-3 font-semibold text-slate-900">24 Okt 2026</td>
                                            <td class="py-3 px-3 text-slate-400">-</td>
                                            <td class="py-3 px-3">Sesi Pagi (06:00 - 08:00 WIB)</td>
                                            <td class="py-3 px-3 text-center">
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">IZIN</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </section>

            <!-- TAB 3: KEHADIRAN PELATIH (KHUSUS ADMIN) -->
            @if((Auth::user()->role ?? 'murid') === 'admin')
                <section id="tab-kehadiran-pelatih" class="space-y-6 hidden">
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Presensi Staf Pelatih</h3>
                                <p class="text-xs text-slate-500">Mencatat tugas pelatih pada setiap sesi renang</p>
                            </div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 bg-amber-50 px-3 py-1 rounded-full border border-amber-200">
                                Otoritas Admin
                            </span>
                        </div>

                        <form onsubmit="event.preventDefault(); alert('Presensi pelatih tersimpan!');" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Pilih Pelatih</label>
                                <select class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
                                    <option value="">-- Pilih Nama Pelatih --</option>
                                    @forelse($coaches ?? [] as $coach)
                                        <option value="{{ $coach->id }}">{{ $coach->name }}</option>
                                    @empty
                                        <option>Coach Bima</option>
                                        <option>Coach Rian</option>
                                        <option>Coach Sarah</option>
                                    @endforelse
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Tugas Sesi</label>
                                <select class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
                                    <option>Sesi Pagi (06:00 - 08:00 WIB)</option>
                                    <option>Sesi Sore (15:30 - 17:30 WIB)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Status Kehadiran</label>
                                <select class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:outline-none">
                                    <option>Bertugas (Hadir)</option>
                                    <option>Izin</option>
                                    <option>Sakit</option>
                                    <option>Tidak Hadir</option>
                                </select>
                            </div>

                            <div class="flex items-end">
                                <button type="submit" class="w-full bg-[#0275d8] hover:bg-sky-600 text-white font-semibold py-2.5 px-4 rounded-xl transition duration-150 flex items-center justify-center space-x-2">
                                    <i class="fa-solid fa-check text-xs"></i>
                                    <span>Simpan Presensi</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Tabel Daftar Pelatih -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                        <h3 class="text-base font-bold text-slate-900 mb-4">Daftar Instruktur / Pelatih</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-100 text-slate-400 uppercase font-semibold">
                                        <th class="pb-3 px-3">Nama Pelatih</th>
                                        <th class="pb-3 px-3">Keahlian Gaya</th>
                                        <th class="pb-3 px-3">Status Tugas</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700">
                                    <tr>
                                        <td class="py-3 px-3 font-semibold text-slate-900">Coach Bima</td>
                                        <td class="py-3 px-3 text-slate-500">Spesialis Dada & Gaya Kupu-kupu</td>
                                        <td class="py-3 px-3">
                                            <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-1 rounded-full text-[10px] font-semibold">Aktif Mengajar</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-3 px-3 font-semibold text-slate-900">Coach Rian</td>
                                        <td class="py-3 px-3 text-slate-500">Spesialis Gaya Bebas & Sprint</td>
                                        <td class="py-3 px-3">
                                            <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-1 rounded-full text-[10px] font-semibold">Aktif Mengajar</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            @endif

            <!-- TAB 4: SCANNER KAMERA (KHUSUS ADMIN) -->
            @if((Auth::user()->role ?? 'murid') === 'admin')
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
                            <!-- Frame Kamera Scanner -->
                            <div class="bg-slate-50 border-2 border-dashed border-slate-300 rounded-2xl p-4 flex flex-col items-center justify-center min-h-[300px]">
                                <div id="reader" class="w-full max-w-sm rounded-xl overflow-hidden shadow-inner"></div>
                                <span class="text-xs text-slate-400 mt-2"><i class="fa-solid fa-video mr-1"></i> Posisikan QR Code berada di dalam kotak pemindai</span>
                            </div>

                            <!-- Hasil Pemindaian Real-time -->
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
                                    Kamera siap memindai kartu QR Code atlet.
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @endif

            <!-- TAB 5: KELOLA AKUN & BARCODE (KHUSUS ADMIN) -->
            @if((Auth::user()->role ?? 'murid') === 'admin')
                <section id="tab-kelola-akun" class="space-y-6 hidden">
                    <!-- Form Tambah Akun Baru -->
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
                        <h3 class="text-base font-bold text-slate-900 mb-1">Daftarkan Akun Anggota Baru</h3>
                        <p class="text-xs text-slate-500 mb-4">Pendaftaran hanya dapat dilakukan oleh Admin. Sistem otomatis membuatkan token Barcode QR.</p>

                        <form onsubmit="handleCreateAccount(event)" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                                <input type="text" id="acc_name" required placeholder="Nama anggota" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Email</label>
                                <input type="email" id="acc_email" required placeholder="email@tirta.com" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">No. WhatsApp</label>
                                <input type="text" id="acc_phone" placeholder="08xxxxxxxxxx" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Role / Peran</label>
                                <select id="acc_role" required class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                                    <option value="murid">Atlet (Murid)</option>
                                    <option value="pelatih">Pelatih (Coach)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Password</label>
                                <input type="password" id="acc_password" required placeholder="Min 6 karakter" class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-500">
                            </div>
                            <div class="sm:col-span-2 lg:col-span-5 flex justify-end mt-2">
                                <button type="submit" class="bg-[#0275d8] hover:bg-sky-600 text-white font-semibold px-5 py-2.5 rounded-xl text-xs flex items-center space-x-2 transition">
                                    <i class="fa-solid fa-user-plus"></i>
                                    <span>Simpan & Terbitkan QR Code</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Tabel Akun & Barcode -->
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200">
                        <h3 class="text-base font-bold text-slate-900 mb-1">Daftar Akun & Barcode Absensi</h3>
                        <p class="text-xs text-slate-500 mb-4">Klik tombol "Lihat QR" untuk melihat, mengunduh, atau mencetak kartu barcode atlet.</p>

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
                                            <td class="py-3 px-3">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                                    {{ $u->role === 'pelatih' ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                                    {{ $u->role }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-3 font-mono text-slate-500">{{ $u->barcode_token ?? ('TB-'.strtoupper(substr($u->role, 0, 1)).'-'.$u->id) }}</td>
                                            <td class="py-3 px-3 text-slate-500">{{ $u->phone ?? '-' }}</td>
                                            <td class="py-3 px-3 text-center">
                                                <button onclick="showQrModal('{{ $u->name }}', '{{ $u->role }}', '{{ $u->barcode_token ?? ('TB-'.strtoupper(substr($u->role, 0, 1)).'-'.$u->id) }}')" 
                                                    class="bg-slate-100 hover:bg-sky-50 text-sky-600 border border-slate-200 hover:border-sky-300 px-3 py-1.5 rounded-lg text-xs font-semibold inline-flex items-center space-x-1">
                                                    <i class="fa-solid fa-qrcode"></i>
                                                    <span>Lihat QR</span>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <!-- Dummy Akun jika Database Belum Terisi -->
                                        <tr>
                                            <td class="py-3 px-3 font-semibold text-slate-900">Dimas Anggara</td>
                                            <td class="py-3 px-3"><span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">murid</span></td>
                                            <td class="py-3 px-3 font-mono text-slate-500">TB-M-101</td>
                                            <td class="py-3 px-3 text-slate-500">08123456789</td>
                                            <td class="py-3 px-3 text-center">
                                                <button onclick="showQrModal('Dimas Anggara', 'murid', 'TB-M-101')" class="bg-slate-100 hover:bg-sky-50 text-sky-600 border border-slate-200 px-3 py-1.5 rounded-lg text-xs font-semibold inline-flex items-center space-x-1">
                                                    <i class="fa-solid fa-qrcode"></i>
                                                    <span>Lihat QR</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-3 px-3 font-semibold text-slate-900">Coach Bima</td>
                                            <td class="py-3 px-3"><span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-sky-50 text-sky-700 border border-sky-200">pelatih</span></td>
                                            <td class="py-3 px-3 font-mono text-slate-500">TB-P-202</td>
                                            <td class="py-3 px-3 text-slate-500">08987654321</td>
                                            <td class="py-3 px-3 text-center">
                                                <button onclick="showQrModal('Coach Bima', 'pelatih', 'TB-P-202')" class="bg-slate-100 hover:bg-sky-50 text-sky-600 border border-slate-200 px-3 py-1.5 rounded-lg text-xs font-semibold inline-flex items-center space-x-1">
                                                    <i class="fa-solid fa-qrcode"></i>
                                                    <span>Lihat QR</span>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            @endif

        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-slate-200 px-6 py-4 text-xs text-slate-400 flex flex-col sm:flex-row justify-between items-center gap-2">
            <span>Tirta Bima Swimming Club — Sistem Manajemen Multi-Role</span>
            <span>SEJAK 2018</span>
        </footer>

    </div>

    <!-- ========================================== -->
    <!-- MODAL POPUP PREVIEW QR CODE                -->
    <!-- ========================================== -->
    <div id="qrModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
        <div class="bg-white rounded-3xl p-6 max-w-sm w-full text-center shadow-2xl relative border border-slate-200">
            <button onclick="closeQrModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
            <span class="text-[10px] font-bold uppercase tracking-widest text-sky-600">Kartu Presensi Tirta Bima</span>
            <h4 id="modalName" class="text-lg font-bold text-slate-900 mt-1">-</h4>
            <p id="modalRole" class="text-xs text-slate-500 mb-4 font-semibold uppercase tracking-wider">-</p>

            <!-- Gambar QR Code -->
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 inline-block mb-3">
                <img id="modalQrImg" src="" alt="QR Code" class="w-48 h-48 mx-auto rounded-lg">
            </div>
            <p id="modalToken" class="text-xs font-mono text-slate-400 mb-4 tracking-wider">-</p>

            <button onclick="window.print()" class="w-full bg-[#0275d8] hover:bg-sky-600 text-white font-medium py-2 rounded-xl text-xs flex items-center justify-center space-x-2 transition">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Kartu QR</span>
            </button>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SCRIPT LOGIKA DASHBOARD, SCANNER & QR      -->
    <!-- ========================================== -->
    <script>
        // 1. Pergantian Tab Sidebar
        function switchTab(target) {
            const tabs = ['beranda', 'kehadiran-atlet', 'kehadiran-pelatih', 'scanner-kamera', 'kelola-akun'];
            
            tabs.forEach(tab => {
                const sec = document.getElementById('tab-' + tab);
                const btn = document.getElementById('btn-' + tab);
                
                if (sec && btn) {
                    if (tab === target) {
                        sec.classList.remove('hidden');
                        btn.classList.add('bg-[#0275d8]', 'text-white');
                        btn.classList.remove('text-slate-300', 'hover:bg-white/10', 'hover:text-white');
                    } else {
                        sec.classList.add('hidden');
                        btn.classList.remove('bg-[#0275d8]', 'text-white');
                        btn.classList.add('text-slate-300', 'hover:bg-white/10', 'hover:text-white');
                    }
                }
            });

            // Tutup kamera otomatis jika berpindah tab dari scanner
            if (target !== 'scanner-kamera') {
                stopScanner();
            }

            const pageTitle = document.getElementById('page-title');
            const pageSubtitle = document.getElementById('page-subtitle');

            if (target === 'beranda') {
                pageTitle.textContent = 'Beranda Dashboard';
                pageSubtitle.textContent = 'Ringkasan aktivitas klub renang';
            } else if (target === 'kehadiran-atlet') {
                pageTitle.textContent = 'Kehadiran Atlet';
                pageSubtitle.textContent = 'Data absensi sesi latihan renang';
            } else if (target === 'kehadiran-pelatih') {
                pageTitle.textContent = 'Kehadiran Pelatih';
                pageSubtitle.textContent = 'Rekap jadwal dan presensi staf instruktur';
            } else if (target === 'scanner-kamera') {
                pageTitle.textContent = 'Pemindai Barcode / QR Code';
                pageSubtitle.textContent = 'Absensi atlet otomatis menggunakan kamera perangkat';
            } else if (target === 'kelola-akun') {
                pageTitle.textContent = 'Kelola Akun & Barcode';
                pageSubtitle.textContent = 'Pendaftaran akun atlet & pelatih beserta pembuatan QR token';
            }
        }

        // 2. Modal QR Code Preview
        function showQrModal(name, role, token) {
            document.getElementById('modalName').textContent = name;
            document.getElementById('modalRole').textContent = role;
            document.getElementById('modalToken').textContent = token;
            
            // Generate QR via api.qrserver.com
            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(token)}`;
            document.getElementById('modalQrImg').src = qrUrl;
            
            document.getElementById('qrModal').classList.remove('hidden');
        }

        function closeQrModal() {
            document.getElementById('qrModal').classList.add('hidden');
        }

        // 3. Tambah Akun Baru (Frontend Handler)
        function handleCreateAccount(e) {
            e.preventDefault();
            const name = document.getElementById('acc_name').value;
            const email = document.getElementById('acc_email').value;
            const phone = document.getElementById('acc_phone').value || '-';
            const role = document.getElementById('acc_role').value;
            const token = 'TB-' + (role === 'pelatih' ? 'P' : 'M') + '-' + Math.floor(100 + Math.random() * 900);

            // Tambahkan baris baru ke tabel
            const tbody = document.getElementById('tabel-akun-body');
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="py-3 px-3 font-semibold text-slate-900">${name}</td>
                <td class="py-3 px-3">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase ${role === 'pelatih' ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200'}">
                        ${role}
                    </span>
                </td>
                <td class="py-3 px-3 font-mono text-slate-500">${token}</td>
                <td class="py-3 px-3 text-slate-500">${phone}</td>
                <td class="py-3 px-3 text-center">
                    <button onclick="showQrModal('${name}', '${role}', '${token}')" class="bg-slate-100 hover:bg-sky-50 text-sky-600 border border-slate-200 px-3 py-1.5 rounded-lg text-xs font-semibold inline-flex items-center space-x-1">
                        <i class="fa-solid fa-qrcode"></i>
                        <span>Lihat QR</span>
                    </button>
                </td>
            `;
            tbody.prepend(tr);

            alert(`Akun ${name} (${role}) berhasil didaftarkan dengan token: ${token}`);
            e.target.reset();
        }

        // 4. Input Manual Presensi
        function handleManualAbsen(e) {
            e.preventDefault();
            const nama = document.getElementById('manual_nama').value;
            const sesi = document.getElementById('manual_sesi').value;
            const status = document.getElementById('manual_status').value;
            
            const now = new Date();
            const timeStr = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0') + ' WIB';

            tambahDataKeTabelPresensi(nama, timeStr, sesi, status);
            alert(`Presensi ${nama} berhasil disimpan dengan status ${status}`);
        }

        // 5. Scanner Kamera & Logika Keterlambatan (Batas: 15:30 WIB)
        let html5QrCode = null;

        function startScanner() {
            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("reader");
            }
            
            html5QrCode.start(
                { facingMode: "environment" },
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 }
                },
                (decodedText, decodedResult) => {
                    // Ketika Barcode/QR berhasil dibaca kamera
                    prosesHasilScan(decodedText);
                    
                    // Jeda sejenak 3 detik sebelum siap scan lagi
                    html5QrCode.pause();
                    setTimeout(() => { 
                        if(html5QrCode) html5QrCode.resume(); 
                    }, 3000);
                },
                (errorMessage) => {
                    // scanning progress
                }
            ).catch(err => {
                alert("Akses kamera ditolak atau tidak ditemukan: " + err);
            });
        }

        function stopScanner() {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    document.getElementById('reader').innerHTML = '';
                    html5QrCode = null;
                }).catch(() => {});
            }
        }

        // Logika Evaluasi Jam Absen (15:30 WIB)
        function prosesHasilScan(token) {
            const now = new Date();
            const jam = now.getHours();
            const menit = now.getMinutes();
            const totalMenitSekarang = jam * 60 + menit;
            const batasMenit1530 = 15 * 60 + 30; // 15:30 WIB = 930 menit

            const timeStr = String(jam).padStart(2, '0') + ':' + String(menit).padStart(2, '0') + ' WIB';
            
            let status = 'HADIR';
            let notes = 'Hadir Tepat Waktu';

            // Pengecekan apakah waktu scan melebihi 15:30 WIB
            if (totalMenitSekarang > batasMenit1530) {
                const selisih = totalMenitSekarang - batasMenit1530;
                status = 'TELAT';
                notes = `Terlambat ${selisih} Menit (Batas: 15:30 WIB)`;
            }

            // Tampilkan kartu hasil pemindaian
            document.getElementById('scan-placeholder').classList.add('hidden');
            document.getElementById('scan-result-card').classList.remove('hidden');

            document.getElementById('res-name').textContent = "Member (" + token + ")";
            document.getElementById('res-token').textContent = "Token: " + token;
            document.getElementById('res-time').textContent = timeStr;
            document.getElementById('res-notes').textContent = notes;

            const badge = document.getElementById('res-status-badge');
            const box = document.getElementById('result-box');
            badge.textContent = status;

            if (status === 'HADIR') {
                box.className = "p-4 rounded-xl border bg-emerald-50 border-emerald-200 text-emerald-900";
                badge.className = "px-2.5 py-1 rounded-full text-xs font-bold uppercase bg-emerald-200 text-emerald-800";
            } else {
                box.className = "p-4 rounded-xl border bg-red-50 border-red-200 text-red-900";
                badge.className = "px-2.5 py-1 rounded-full text-xs font-bold uppercase bg-red-200 text-red-800";
            }

            // Masukkan data hasil scan ke tabel presensi hari ini
            tambahDataKeTabelPresensi("Member (" + token + ")", timeStr, "Sesi Sore (15:30 WIB)", status);
        }

        function tambahDataKeTabelPresensi(nama, waktu, sesi, status) {
            const tbody = document.getElementById('tabel-presensi-body');
            if (!tbody) return;

            let badgeClass = 'bg-emerald-50 text-emerald-700 border border-emerald-200';
            if (status === 'TELAT' || status === 'ALFA') {
                badgeClass = 'bg-red-50 text-red-700 border border-red-200';
            } else if (status === 'IZIN' || status === 'SAKIT') {
                badgeClass = 'bg-amber-50 text-amber-700 border border-amber-200';
            }

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="py-3 px-3 font-semibold text-slate-900">${nama}</td>
                <td class="py-3 px-3 text-slate-500">${waktu}</td>
                <td class="py-3 px-3">${sesi}</td>
                <td class="py-3 px-3 text-center">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold ${badgeClass}">${status}</span>
                </td>
            `;
            tbody.prepend(tr);
        }
    </script>
</body>
</html>
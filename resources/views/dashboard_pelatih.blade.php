<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tirta Bima</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-100 min-h-screen font-sans antialiased text-slate-800 flex flex-col md:flex-row">

    <!-- SIDEBAR PELATIH -->
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
                        {{ strtoupper(substr(Auth::user()->name ?? 'P', 0, 1)) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-xs font-semibold text-white truncate">{{ Auth::user()->name ?? 'Coach' }}</p>
                        <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wide bg-sky-400/20 text-sky-300 border border-sky-400/30">
                            Pelatih Resmi
                        </span>
                    </div>
                </div>
            </div>

            <!-- Menu Sidebar -->
            <nav class="p-4 space-y-1.5 text-xs font-medium">
                <button onclick="switchTab('beranda')" id="btn-beranda" class="nav-tab w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition bg-[#0275d8] text-white">
                    <i class="fa-solid fa-table-columns text-sm w-5 text-center"></i>
                    <span>Beranda Pelatih</span>
                </button>
                <button onclick="switchTab('kehadiran-atlet')" id="btn-kehadiran-atlet" class="nav-tab w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition text-slate-300 hover:bg-white/10 hover:text-white">
                    <i class="fa-solid fa-clipboard-user text-sm w-5 text-center"></i>
                    <span>Kehadiran Atlet</span>
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

    <!-- KONTEN UTAMA PELATIH -->
    <div class="flex-1 flex flex-col min-h-screen overflow-y-auto">
        <header class="bg-white border-b border-slate-200 px-6 lg:px-8 py-4 flex items-center justify-between sticky top-0 z-10">
            <div>
                <h1 id="page-title" class="text-lg font-bold text-slate-900">Beranda Pelatih</h1>
                <p id="page-subtitle" class="text-xs text-slate-500">Monitoring atlet bimbingan & jadwal sesi renang</p>
            </div>
            <div class="text-xs text-slate-500 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200">
                <i class="fa-regular fa-calendar-check text-sky-600 mr-1"></i> {{ date('d F Y') }}
            </div>
        </header>

        <main class="p-6 lg:p-8 flex-1 space-y-6">
            <!-- TAB 1: BERANDA -->
            <section id="tab-beranda" class="space-y-6">
                <div class="bg-[#0b2447] text-white rounded-3xl p-8 relative overflow-hidden shadow-lg">
                    <div class="relative z-10 max-w-2xl">
                        <span class="text-xs font-bold uppercase tracking-widest text-sky-400">Portal Instruktur & Pelatih</span>
                        <h2 class="text-2xl sm:text-3xl font-extrabold mt-1">Semangat Melatih, {{ Auth::user()->name }}!</h2>
                        <p class="text-slate-300 text-xs sm:text-sm mt-2 leading-relaxed">
                            Pantau kedisiplinan dan kehadiran atlet bimbinganmu pada sesi latihan hari ini.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                        <p class="text-[11px] font-semibold text-slate-500 uppercase">Total Atlet</p>
                        <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ count($athletes ?? []) > 0 ? count($athletes) : '48' }} Atlet</h3>
                    </div>
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                        <p class="text-[11px] font-semibold text-slate-500 uppercase">Hadir Sore Ini</p>
                        <h3 class="text-2xl font-bold text-emerald-600 mt-1">42 Atlet</h3>
                    </div>
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                        <p class="text-[11px] font-semibold text-slate-500 uppercase">Jadwal Sesi</p>
                        <h3 class="text-2xl font-bold text-sky-600 mt-1">15:30 WIB</h3>
                    </div>
                </div>
            </section>

            <!-- TAB 2: KEHADIRAN ATLET -->
            <section id="tab-kehadiran-atlet" class="space-y-6 hidden">
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
                    <h3 class="text-base font-bold text-slate-900 mb-1">Presensi Manual Atlet Bimbingan</h3>
                    <p class="text-xs text-slate-500 mb-4">Catat status izin atau sakit bagi atlet yang berhalangan hadir</p>

                    <form onsubmit="event.preventDefault(); alert('Presensi dicatat!');" class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Pilih Atlet</label>
                            <select class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                                @forelse($athletes ?? [] as $a)
                                    <option value="{{ $a->name }}">{{ $a->name }}</option>
                                @empty
                                    <option>Dimas Anggara</option>
                                    <option>Nadia Lestari</option>
                                @endforelse
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Status</label>
                            <select class="w-full px-3 py-2 border border-slate-300 rounded-xl">
                                <option value="HADIR">Hadir</option>
                                <option value="IZIN">Izin</option>
                                <option value="SAKIT">Sakit</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-[#0275d8] hover:bg-sky-600 text-white font-semibold py-2.5 rounded-xl transition">
                                Simpan Presensi
                            </button>
                        </div>
                    </form>
                </div>
            </section>
        </main>
    </div>

    <script>
        function switchTab(target) {
            ['beranda', 'kehadiran-atlet'].forEach(tab => {
                const sec = document.getElementById('tab-' + tab);
                const btn = document.getElementById('btn-' + tab);
                if (tab === target) {
                    sec.classList.remove('hidden');
                    btn.classList.add('bg-[#0275d8]', 'text-white');
                    btn.classList.remove('text-slate-300', 'hover:bg-white/10', 'hover:text-white');
                } else {
                    sec.classList.add('hidden');
                    btn.classList.remove('bg-[#0275d8]', 'text-white');
                    btn.classList.add('text-slate-300', 'hover:bg-white/10', 'hover:text-white');
                }
            });
        }
    </script>
</body>
</html>
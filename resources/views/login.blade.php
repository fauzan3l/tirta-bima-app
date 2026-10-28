<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tirta — Masuk</title>
    <!-- Tailwind CSS & Font Awesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen p-4 sm:p-6 lg:p-8 font-sans antialiased text-slate-800">

    <div class="bg-white rounded-3xl shadow-xl overflow-hidden max-w-5xl w-full flex flex-col md:flex-row min-h-[640px]">
        
        <!-- Sisi Kiri: Branding Tirta Bima Swimming Club -->
        <div class="bg-[#0b2447] text-white p-8 md:p-12 md:w-5/12 flex flex-col justify-between relative overflow-hidden">
            <div>
                <div class="flex items-center space-x-2 text-lg font-semibold tracking-wide">
                    <svg class="w-6 h-6 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span>Tirta Bima Swimming Club</span>
                </div>
                
                <div class="mt-12">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-sky-400">Kolam Renang & Komunitas</span>
                    <h1 class="text-3xl md:text-4xl font-bold mt-2 leading-tight">
                        Jeda sejenak.<br>Segarkan harimu.
                    </h1>
                    <p class="text-slate-300 text-sm mt-4 leading-relaxed">
                        Kembali ke air, kembali ke diri sendiri. Atur kunjungan dan nikmati waktu berenangmu bersama Tirta.
                    </p>
                </div>
            </div>

            <!-- Ilustrasi Kolam Renang -->
            <div class="my-8 flex justify-center">
                <div class="w-64 h-32 rounded-full border-4 border-sky-400/80 bg-sky-600/30 relative flex items-center justify-center overflow-hidden">
                    <div class="w-full border-t border-dashed border-sky-300/40 absolute top-1/3"></div>
                    <div class="w-full border-t border-dashed border-sky-300/40 absolute bottom-1/3"></div>
                    <div class="text-sky-200">
                        <i class="fa-solid fa-person-swimming text-3xl"></i>
                    </div>
                </div>
            </div>

            <!-- Footer Sisi Kiri -->
            <div class="text-xs text-slate-400 flex justify-between items-center border-t border-slate-700/50 pt-4">
                <span>Tirta Bima Swimming Club — Atlet Renang</span>
                <span>SEJAK 2018</span>
            </div>
        </div>

        <!-- Sisi Kanan: Form Login -->
        <div class="p-8 md:p-12 md:w-7/12 flex flex-col justify-center">
            <div class="max-w-md w-full mx-auto">
                <div class="text-center mb-6">
                    <span class="text-xs font-bold text-sky-600 uppercase tracking-widest">Masuk ke Tirta</span>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 mt-1">Selamat datang kembali.</h2>
                    <p class="text-sm text-slate-500 mt-1">Masuk untuk merencanakan kunjungan berenang berikutnya.</p>
                </div>

                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-xs rounded">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Input Email -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                            placeholder="nama@email.com"
                            class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent text-sm placeholder-slate-400">
                    </div>

                    <!-- Input Password -->
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Kata sandi</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                placeholder="Masukkan kata sandi"
                                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent text-sm placeholder-slate-400 pr-24">
                            <button type="button" onclick="togglePassword('password', 'icon-pass')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs text-slate-500 hover:text-slate-800 space-x-1">
                                <i id="icon-pass" class="fa-regular fa-eye"></i>
                                <span>Tampilkan</span>
                            </button>
                        </div>
                    </div>

                    <!-- Ingat Saya & Lupa Kata Sandi -->
                    <div class="flex items-center justify-between text-xs">
                        <label class="flex items-center space-x-2 text-slate-600">
                            <input type="checkbox" name="remember" class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                            <span>Ingat saya</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sky-600 hover:underline font-semibold">Lupa kata sandi?</a>
                        @endif
                    </div>

                    <!-- Tombol Masuk -->
                    <button type="submit" class="w-full bg-[#0275d8] hover:bg-sky-600 text-white font-medium py-2.5 rounded-lg flex items-center justify-center space-x-2 transition duration-150">
                        <span>Masuk</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </form>

                <p class="text-center text-xs text-slate-500 mt-6">
                    Belum punya akun? 
                    <a href="{{ route('register') }}" class="text-sky-600 font-semibold hover:underline">Daftar sekarang</a>
                </p>
            </div>
        </div>

    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
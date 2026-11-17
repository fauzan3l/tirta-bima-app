<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdminController extends Controller
{
    // 0. Halaman Utama Dashboard Admin
    public function index()
    {
        $athletes = User::where('role', 'murid')->orderBy('name')->get();
        $coaches  = User::where('role', 'pelatih')->orderBy('name')->get();

        $today = Carbon::now('Asia/Jakarta')->toDateString();

        // Ambil data absensi Atlet hari ini
        $athleteAttendances = Attendance::with('user')
            ->whereHas('user', fn ($q) => $q->where('role', 'murid'))
            ->whereDate('date', $today)
            ->orderByDesc('time')
            ->get();

        // Ambil data absensi Pelatih hari ini
        $coachAttendances = Attendance::with('user')
            ->whereHas('user', fn ($q) => $q->where('role', 'pelatih'))
            ->whereDate('date', $today)
            ->orderByDesc('time')
            ->get();

        // Total Hadir / Telat hari ini untuk kartu Beranda Dashboard
        $hadirHariIni = Attendance::whereDate('date', $today)
            ->whereIn('status', ['hadir', 'telat', 'HADIR', 'TELAT'])
            ->count();

        return view('dashboard_admin', compact(
            'athletes',
            'coaches',
            'athleteAttendances',
            'coachAttendances',
            'hadirHariIni'
        ));
    }

    // 1. Simpan Akun Baru (Atlet atau Pelatih) oleh Admin
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:20',
            'role'     => 'required|in:pelatih,murid',
            'password' => 'required|min:6',
        ]);

        $token = 'TB-' . strtoupper(substr($validated['role'], 0, 1)) . '-' . Str::random(6);

        User::create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'phone'             => $validated['phone'],
            'role'              => $validated['role'],
            'password'          => Hash::make($validated['password']),
            'barcode_token'     => $token,
            'terms_accepted_at' => now(),
        ]);

        return redirect()->to(url()->previous() . '#kelola-akun')
            ->with('success', 'Akun ' . ucfirst($validated['role']) . ' berhasil dibuat beserta QR Code-nya.');
    }

    // 2. Proses Hasil Scan Barcode / QR Code
    public function scanAttendance(Request $request)
    {
        $request->validate([
            'barcode_token' => 'required|string',
        ]);

        $token = trim($request->barcode_token);
        $user  = User::where('barcode_token', $token)->first();

        // Fallback jika memakai format TB-M-1 atau TB-P-2
        if (!$user && preg_match('/^TB-[MP]-(\d+)$/i', $token, $matches)) {
            $user = User::find($matches[1]);
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Barcode / Akun tidak terdaftar di sistem Tirta Bima!',
            ], 404);
        }

        $now   = Carbon::now('Asia/Jakarta');
        $today = $now->toDateString();

        // Cek apakah sudah absen hari ini
        $alreadyScanned = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if ($alreadyScanned) {
            return response()->json([
                'success' => false,
                'message' => "{$user->name} sudah melakukan absensi hari ini pada " . substr($alreadyScanned->time, 0, 5) . " WIB.",
            ], 400);
        }

        // Jam batas: 15:30 WIB
        $sessionTimeLimit = Carbon::today('Asia/Jakarta')->setTime(15, 30, 0);

        $status = 'hadir';
        $note   = 'Hadir Tepat Waktu';

        if ($now->greaterThan($sessionTimeLimit)) {
            $status       = 'telat';
            $selisihMenit = $sessionTimeLimit->diffInMinutes($now);
            $note         = "Terlambat {$selisihMenit} menit (Batas 15:30 WIB)";
        }

        // Catat ke database sesuai kolom tabel attendances
        Attendance::create([
            'user_id'      => $user->id,
            'recorded_by'  => Auth::id(),
            'date'         => $today,
            'time'         => $now->format('H:i:s'),
            'session_name' => 'Sesi Sore (15:30 WIB)',
            'status'       => $status,
            'notes'        => $note,
        ]);

        $totalHadirHari = Attendance::whereDate('date', $today)
            ->whereIn('status', ['hadir', 'telat', 'HADIR', 'TELAT'])
            ->count();

        return response()->json([
            'success'          => true,
            'user_name'        => $user->name,
            'role'             => strtolower($user->role),
            'token'            => $token,
            'status'           => strtoupper($status),
            'notes'            => $note,
            'sesi'             => 'Sesi Sore (15:30 WIB)',
            'time'             => $now->format('H:i') . ' WIB',
            'total_hadir_hari' => $totalHadirHari,
        ]);
    }

    // 3. Simpan Absensi Manual (Atlet & Pelatih)
    public function manualAttendance(Request $request)
    {
        $request->validate([
            'name'   => 'required|string',
            'sesi'   => 'required|string',
            'status' => 'required|string',
        ]);

        $user = User::where('name', $request->name)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Data anggota tidak ditemukan di database.',
            ], 404);
        }

        $now         = Carbon::now('Asia/Jakarta');
        $today       = $now->toDateString();
        $statusLower = strtolower($request->status);

        Attendance::updateOrCreate(
            [
                'user_id' => $user->id,
                'date'    => $today,
            ],
            [
                'recorded_by'  => Auth::id(),
                'time'         => $now->format('H:i:s'),
                'session_name' => $request->sesi,
                'status'       => $statusLower,
                'notes'        => 'Input manual oleh Administrator',
            ]
        );

        $totalHadirHari = Attendance::whereDate('date', $today)
            ->whereIn('status', ['hadir', 'telat', 'HADIR', 'TELAT'])
            ->count();

        return response()->json([
            'success'          => true,
            'user_name'        => $user->name,
            'role'             => strtolower($user->role),
            'status'           => strtoupper($statusLower),
            'sesi'             => $request->sesi,
            'time'             => $now->format('H:i') . ' WIB',
            'total_hadir_hari' => $totalHadirHari,
        ]);
    }
}
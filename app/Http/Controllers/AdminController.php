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

        // Buat barcode_token unik, misalnya: TB-2026-XXXX
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

        return back()->with('success', 'Akun ' . ucfirst($validated['role']) . ' berhasil dibuat beserta QR Code-nya.');
    }

    // 2. Proses Hasil Scan Barcode / QR Code
    public function scanAttendance(Request $request)
    {
        $request->validate([
            'barcode_token' => 'required|string',
        ]);

        $user = User::where('barcode_token', $request->barcode_token)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Barcode / Akun tidak terdaftar di sistem Tirta Bima!',
            ], 404);
        }

        $now = Carbon::now();
        $today = $now->toDateString();

        // Cek apakah sudah absen hari ini
        $alreadyScanned = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if ($alreadyScanned) {
            return response()->json([
                'success' => false,
                'message' => "{$user->name} sudah melakukan absensi hari ini pada {$alreadyScanned->time} WIB.",
            ], 400);
        }

        // Jam batas: 15:30 (Setengah 4 sore)
        $sessionTimeLimit = Carbon::createFromTime(15, 30, 0);

        // Jika waktu sekarang melewati 15:30 WIB -> Dianggap Telat
        $status = 'hadir';
        $note = 'Hadir Tepat Waktu';

        if ($now->greaterThan($sessionTimeLimit)) {
            $status = 'telat';
            $selisihMenit = $sessionTimeLimit->diffInMinutes($now);
            $note = "Terlambat {$selisihMenit} menit (Batas 15:30 WIB)";
        }

        // Catat ke database
        $attendance = Attendance::create([
            'user_id'      => $user->id,
            'recorded_by'  => Auth::id(),
            'date'         => $today,
            'time'         => $now->format('H:i:s'),
            'session_name' => 'Sesi Renang Sore',
            'status'       => $status,
            'notes'        => $note,
        ]);

        return response()->json([
            'success'   => true,
            'user_name' => $user->name,
            'role'      => ucfirst($user->role),
            'status'    => strtoupper($status),
            'notes'     => $note,
            'time'      => $now->format('H:i') . ' WIB',
        ]);
    }
}
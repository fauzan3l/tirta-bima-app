<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'phone'    => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:murid,pelatih,admin',
            'kelompok' => 'nullable|in:Learn To Swim,Intermediate,Champions',
        ]);

        $prefix = match ($validated['role']) {
            'pelatih' => 'TB-P-',
            'admin'   => 'TB-A-',
            default   => 'TB-M-',
        };

        $user = User::create([
            'name'              => $validated['name'],
            'email'             => $validated['email'],
            'phone'             => $validated['phone'] ?? null,
            'password'          => Hash::make($validated['password']),
            'role'              => $validated['role'],
            'kelompok'          => $validated['role'] === 'admin' ? null : ($validated['kelompok'] ?? 'Learn To Swim'),
            'barcode_token'     => $prefix . strtoupper(Str::random(6)),
            'terms_accepted_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Akun ' . ucfirst($user->role) . ' berhasil ditambahkan.',
            'user'    => $user,
        ]);
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Akun tidak ditemukan.',
            ], 404);
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'    => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
            'role'     => 'required|in:murid,pelatih,admin',
            'kelompok' => 'nullable|in:Learn To Swim,Intermediate,Champions',
        ]);

        $updateData = [
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'] ?? null,
            'role'     => $validated['role'],
            'kelompok' => $validated['role'] === 'admin' ? null : ($validated['kelompok'] ?? 'Learn To Swim'),
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Akun ' . $user->name . ' berhasil diperbarui.',
            'user'    => $user->fresh(),
        ]);
    }

    public function deleteUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Akun tidak ditemukan.',
            ], 404);
        }

        if ($user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus akun yang sedang digunakan login.',
            ], 422);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Akun berhasil dihapus.',
        ]);
    }

    public function manualAttendance(Request $request)
    {
        $validated = $request->validate([
            'name'   => 'required|string',
            'sesi'   => 'nullable|string|max:100',
            'status' => 'required|string|max:30',
        ]);

        $user = User::where('name', $validated['name'])->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Pengguna dengan nama tersebut tidak ditemukan.',
            ], 404);
        }

        // Validasi jika yang input adalah pelatih, hanya boleh atlet dari kelompoknya sendiri
        $authUser = Auth::user();
        if ($authUser && $authUser->role === 'pelatih') {
            $coachKelompok = $authUser->kelompok ?? 'Learn To Swim';
            if ($user->role !== 'murid' || ($user->kelompok ?? 'Learn To Swim') !== $coachKelompok) {
                return response()->json([
                    'success' => false,
                    'message' => 'Atlet tersebut bukan dari kelompok ' . $coachKelompok . '.',
                ], 403);
            }
        }

        $now    = Carbon::now('Asia/Jakarta');
        $today  = $now->toDateString();
        $time   = $now->format('H:i:s');
        $sesi   = $validated['sesi'] ?? 'Sesi Sore (15:30 WIB)';
        $status = strtoupper($validated['status']);

        $attendance = Attendance::updateOrCreate(
            [
                'user_id' => $user->id,
                'date'    => $today,
            ],
            [
                'time'         => $time,
                'session_name' => $sesi,
                'status'       => $status,
            ]
        );

        $totalHadirHari = Attendance::whereDate('date', $today)
            ->whereIn('status', ['hadir', 'telat', 'HADIR', 'TELAT'])
            ->count();

        return response()->json([
            'success'          => true,
            'user_name'        => $user->name,
            'role'             => $user->role,
            'kelompok'         => $user->kelompok ?? 'Learn To Swim',
            'time'             => substr($attendance->time, 0, 5) . ' WIB',
            'sesi'             => $attendance->session_name,
            'status'           => strtoupper($attendance->status),
            'total_hadir_hari' => $totalHadirHari,
        ]);
    }

    public function scanAttendance(Request $request)
    {
        $token = $request->input('barcode_token') ?? $request->input('token');
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token barcode tidak boleh kosong.',
            ], 422);
        }

        $user = User::where('barcode_token', trim($token))->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Barcode tidak terdaftar di sistem.',
            ], 404);
        }

        $now   = Carbon::now('Asia/Jakarta');
        $today = $now->toDateString();
        $time  = $now->format('H:i:s');
        $sesi  = $now->hour < 12 ? 'Sesi Pagi (06:00 WIB)' : 'Sesi Sore (15:30 WIB)';

        $attendance = Attendance::updateOrCreate(
            [
                'user_id' => $user->id,
                'date'    => $today,
            ],
            [
                'time'         => $time,
                'session_name' => $sesi,
                'status'       => 'HADIR',
            ]
        );

        $totalHadirHari = Attendance::whereDate('date', $today)
            ->whereIn('status', ['hadir', 'telat', 'HADIR', 'TELAT'])
            ->count();

        return response()->json([
            'success'          => true,
            'message'          => 'Scan berhasil: ' . $user->name . ' tercatat HADIR.',
            'user_name'        => $user->name,
            'role'             => $user->role,
            'kelompok'         => $user->kelompok ?? 'Learn To Swim',
            'time'             => substr($attendance->time, 0, 5) . ' WIB',
            'sesi'             => $attendance->session_name,
            'status'           => 'HADIR',
            'total_hadir_hari' => $totalHadirHari,
        ]);
    }
}
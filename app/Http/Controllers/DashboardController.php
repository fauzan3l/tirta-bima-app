<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\DataAtlet;
use App\Models\ProgramLatihan;
use App\Models\BestTime;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $role = Auth::user()->role ?? 'murid';

        $selectedDate = $request->query('date', Carbon::now('Asia/Jakarta')->toDateString());

        try {
            $parsedDate   = Carbon::parse($selectedDate, 'Asia/Jakarta');
            $selectedDate = $parsedDate->toDateString();
        } catch (\Exception $e) {
            $parsedDate   = Carbon::now('Asia/Jakarta');
            $selectedDate = $parsedDate->toDateString();
        }

        $formattedDate = $parsedDate->locale('id')->translatedFormat('d F Y');
        $events        = Event::orderBy('tanggal_mulai')->orderBy('id')->get();

        if ($role === 'admin') {
            $athletes = User::where('role', 'murid')->orderBy('name')->get();
            $coaches  = User::where('role', 'pelatih')->orderBy('name')->get();

            $athleteAttendances = Attendance::with('user')
                ->whereHas('user', fn ($q) => $q->where('role', 'murid'))
                ->whereDate('date', $selectedDate)
                ->orderByDesc('time')
                ->get();

            $coachAttendances = Attendance::with('user')
                ->whereHas('user', fn ($q) => $q->where('role', 'pelatih'))
                ->whereDate('date', $selectedDate)
                ->orderByDesc('time')
                ->get();

            $hadirHariIni = Attendance::whereDate('date', $selectedDate)
                ->whereIn('status', ['hadir', 'telat', 'HADIR', 'TELAT'])
                ->count();

            return view('dashboard/dashboard_admin', compact(
                'athletes',
                'coaches',
                'athleteAttendances',
                'coachAttendances',
                'hadirHariIni',
                'selectedDate',
                'formattedDate',
                'events'
            ));
        } elseif ($role === 'pelatih') {
            $coachKelompok = Auth::user()->kelompok ?? 'Learn To Swim';
            $isChampions   = $coachKelompok === 'Champions';

            $athletes = User::where('role', 'murid')
                ->where('kelompok', $coachKelompok)
                ->orderBy('name')
                ->get();

            $athleteAttendances = Attendance::with('user')
                ->whereHas('user', fn ($q) => $q->where('role', 'murid')->where('kelompok', $coachKelompok))
                ->whereDate('date', $selectedDate)
                ->orderByDesc('time')
                ->get();

            $hadirHariIni = Attendance::whereHas('user', fn ($q) => $q->where('role', 'murid')->where('kelompok', $coachKelompok))
                ->whereDate('date', $selectedDate)
                ->whereIn('status', ['hadir', 'telat', 'HADIR', 'TELAT'])
                ->count();

            $dataAtlet = DataAtlet::where('kelompok', $coachKelompok)->orderByDesc('id')->get();
            $programs  = $isChampions ? ProgramLatihan::orderByDesc('tanggal')->orderByDesc('id')->get() : collect();
            $bestTimes = $isChampions ? BestTime::orderByDesc('tanggal')->orderByDesc('id')->get() : collect();

            return view('dashboard/dashboard_pelatih', compact(
                'athletes',
                'athleteAttendances',
                'hadirHariIni',
                'dataAtlet',
                'programs',
                'bestTimes',
                'events',
                'selectedDate',
                'formattedDate',
                'coachKelompok',
                'isChampions'
            ));
        } else {
            $user          = Auth::user();
            $muridKelompok = $user->kelompok ?? 'Learn To Swim';

            $myAttendances = Attendance::where('user_id', $user->id)
                ->orderByDesc('date')
                ->orderByDesc('time')
                ->limit(30)
                ->get();

            $totalHadir = Attendance::where('user_id', $user->id)
                ->whereIn('status', ['hadir', 'telat', 'HADIR', 'TELAT'])
                ->count();

            $totalIzinSakit = Attendance::where('user_id', $user->id)
                ->whereIn('status', ['izin', 'sakit', 'IZIN', 'SAKIT'])
                ->count();

            $myBestTimes = BestTime::where('nama_atlet', $user->name)
                ->orderByDesc('tanggal')
                ->orderByDesc('id')
                ->get();

            return view('dashboard/dashboard_murid', compact(
                'muridKelompok',
                'myAttendances',
                'totalHadir',
                'totalIzinSakit',
                'myBestTimes',
                'events',
                'formattedDate'
            ));
        }
    }

    public function storeDataAtlet(Request $request)
    {
        $coachKelompok = Auth::user()->kelompok ?? 'Learn To Swim';

        $validated = $request->validate([
            'id'           => 'nullable|integer|exists:data_atlet,id',
            'name'         => 'required|string|max:255',
            'nik'          => 'nullable|string|max:30',
            'nisnas'       => 'nullable|string|max:30',
            'ttl'          => 'nullable|string|max:100',
            'phone'        => 'nullable|string|max:20',
            'email'        => 'required|email|max:255',
            'tinggi_badan' => 'nullable|numeric',
            'berat_badan'  => 'nullable|numeric',
            'sekolah'      => 'nullable|string|max:150',
            'kelas'        => 'nullable|string|max:50',
        ]);

        $matchedUser = User::where('email', $validated['email'])->first();

        $payload = [
            'user_id'      => $matchedUser?->id,
            'name'         => $validated['name'],
            'kelompok'     => $coachKelompok,
            'nik'          => $validated['nik'],
            'nisnas'       => $validated['nisnas'],
            'ttl'          => $validated['ttl'],
            'phone'        => $validated['phone'],
            'email'        => $validated['email'],
            'tinggi_badan' => $validated['tinggi_badan'],
            'berat_badan'  => $validated['berat_badan'],
            'sekolah'      => $validated['sekolah'],
            'kelas'        => $validated['kelas'],
        ];

        $record = null;

        if (!empty($validated['id'])) {
            $record = DataAtlet::where('id', $validated['id'])
                ->where('kelompok', $coachKelompok)
                ->first();
        }

        if (!$record && $matchedUser) {
            $record = DataAtlet::where('user_id', $matchedUser->id)->first();
        }

        if (!$record) {
            $record = DataAtlet::where('email', $validated['email'])->first();
        }

        if ($record) {
            $record->update($payload);
        } else {
            $record = DataAtlet::create($payload);
        }

        return response()->json([
            'success'    => true,
            'message'    => 'Data atlet berhasil disimpan ke tabel data_atlet.',
            'data_atlet' => $record,
        ]);
    }

    public function deleteDataAtlet($id)
    {
        $coachKelompok = Auth::user()->kelompok ?? 'Learn To Swim';
        $record = DataAtlet::where('id', $id)->where('kelompok', $coachKelompok)->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Data atlet tidak ditemukan.',
            ], 404);
        }

        $record->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data atlet berhasil dihapus.',
        ]);
    }

    public function storeProgram(Request $request)
    {
        if ((Auth::user()->kelompok ?? '') !== 'Champions') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak. Khusus pelatih Champions.'], 403);
        }

        $validated = $request->validate([
            'kelompok'     => 'required|string|max:100',
            'fase'         => 'nullable|string|max:150',
            'judul_minggu' => 'required|string|max:255',
            'target_event' => 'nullable|string|max:255',
            'alat'         => 'nullable|string|max:255',
            'tanggal'      => 'required|date',
            'file_program' => 'required|file|mimes:pdf,xls,xlsx,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $filePath = null;
        $fileName = null;

        if ($request->hasFile('file_program')) {
            $file     = $request->file('file_program');
            $fileName = $file->getClientOriginalName();
            $safeName = time() . '_' . preg_replace('/[^A-Za-z0-9.\-_]/', '_', $fileName);

            $destination = public_path('uploads/program_latihan');
            if (!File::exists($destination)) {
                File::makeDirectory($destination, 0755, true);
            }

            $file->move($destination, $safeName);
            $filePath = '/uploads/program_latihan/' . $safeName;
        }

        $program = ProgramLatihan::create([
            'kelompok'     => $validated['kelompok'],
            'fase'         => $validated['fase'] ?? null,
            'judul_minggu' => $validated['judul_minggu'],
            'target_event' => $validated['target_event'] ?? null,
            'alat'         => $validated['alat'] ?? null,
            'tanggal'      => $validated['tanggal'],
            'file_path'    => $filePath,
            'file_name'    => $fileName,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Program latihan dan file berhasil diupload.',
            'program' => $program,
        ]);
    }

    public function deleteProgram($id)
    {
        if ((Auth::user()->kelompok ?? '') !== 'Champions') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak. Khusus pelatih Champions.'], 403);
        }

        $program = ProgramLatihan::find($id);

        if (!$program) {
            return response()->json([
                'success' => false,
                'message' => 'Program tidak ditemukan.',
            ], 404);
        }

        if ($program->file_path) {
            $fullPath = public_path(ltrim($program->file_path, '/'));
            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }
        }

        $program->delete();

        return response()->json([
            'success' => true,
            'message' => 'Program latihan berhasil dihapus.',
        ]);
    }

    public function storeBestTime(Request $request)
    {
        if ((Auth::user()->kelompok ?? '') !== 'Champions') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak. Khusus pelatih Champions.'], 403);
        }

        $validated = $request->validate([
            'nama_atlet' => 'required|string|max:255',
            'gaya'       => 'required|string|max:50',
            'jarak'      => 'required|string|max:30',
            'waktu'      => 'required|string|max:30',
            'tanggal'    => 'required|date',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $bestTime = BestTime::create($validated);

        return response()->json([
            'success'   => true,
            'message'   => 'Catatan Best Time berhasil disimpan.',
            'best_time' => $bestTime,
        ]);
    }

    public function deleteBestTime($id)
    {
        if ((Auth::user()->kelompok ?? '') !== 'Champions') {
            return response()->json(['success' => false, 'message' => 'Akses ditolak. Khusus pelatih Champions.'], 403);
        }

        $bestTime = BestTime::find($id);

        if (!$bestTime) {
            return response()->json([
                'success' => false,
                'message' => 'Data Best Time tidak ditemukan.',
            ], 404);
        }

        $bestTime->delete();

        return response()->json([
            'success' => true,
            'message' => 'Catatan Best Time berhasil dihapus.',
        ]);
    }

public function storeEvent(Request $request)
    {
        $user = Auth::user();
        $isAllowed = ($user->role ?? '') === 'admin'
            || (($user->role ?? '') === 'pelatih' && ($user->kelompok ?? '') === 'Champions');

        if (!$isAllowed) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak. Hanya Admin atau Pelatih Champions yang dapat menambah event.'], 403);
        }

        $validated =$request->validate([
            'nama_event'      => 'required|string|max:255',
            'tingkat'         => 'required|string|max:100',
            'lokasi'          => 'required|string|max:255',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        $event = Event::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Event berhasil ditambahkan.',
            'event'   => $event,
        ]);
    }

    public function deleteEvent($id)
    {
        $user = Auth::user();
        $isAllowed = ($user->role ?? '') === 'admin'
            || (($user->role ?? '') === 'pelatih' && ($user->kelompok ?? '') === 'Champions');

        if (!$isAllowed) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak. Hanya Admin atau Pelatih Champions yang dapat menghapus event.'], 403);
        }

        $event = Event::find($id);

        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'Event tidak ditemukan.',
            ], 404);
        }

        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event berhasil dihapus.',
        ]);
    }
}
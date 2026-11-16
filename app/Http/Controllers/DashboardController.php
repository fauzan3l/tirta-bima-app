<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $role = Auth::user()->role ?? 'murid';

        $athletes = User::where('role', 'murid')->get();
        $coaches  = User::where('role', 'pelatih')->get();

        if ($role === 'admin') {
            return view('dashboard_admin', compact('athletes', 'coaches'));
        } elseif ($role === 'pelatih') {
            return view('dashboard_pelatih', compact('athletes'));
        } else {
            return view('dashboard_murid');
        }
    }
}
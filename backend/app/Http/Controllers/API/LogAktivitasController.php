<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LogAktivitas;

class LogAktivitasController extends Controller
{
    public function index(Request $request)
    {
        $logAktivitas = LogAktivitas::with('user')->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Data log aktivitas berhasil ditampilkan.',
            'data'    => $logAktivitas
        ], 200);
    }
}
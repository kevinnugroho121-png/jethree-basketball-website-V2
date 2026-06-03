<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notifikasi;

class NotifikasiController extends Controller
{
    public function index(Request $request)
    {
        // Ambil notifikasi milik user yang sedang login
        $notifikasis = Notifikasi::where('user_id', $request->user()->id)
                                 ->orderBy('created_at', 'desc') // Yang baru di atas
                                 ->get();

        return response()->json([
            'success' => true,
            'data'    => $notifikasis
        ]);
    }
}
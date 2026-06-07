<?php

namespace App\Http\Controllers;

use App\Models\IndividualSession; // Sesi Individu
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardSesiIndividuController extends Controller
{
    public function index(Request $request)
    {
        // Pastikan tidak ada angka atau karakter aneh di baris ini
    $query = IndividualSession::where('user_id', Auth::id())
                              ->withCount('individualSubmissions');

    // Fitur Pencarian
    if ($request->filled('search')) {
        $query->where('title', 'like', '%' . $request->search . '%');
    }

    $individualSessions = $query->get();

    // Statistik khusus untuk sesi individu
    $stats = [
        'total'       => $individualSessions->count(),
        'aktif'       => $individualSessions->where('is_active', true)->count(),
        'tidak_aktif' => $individualSessions->where('is_active', false)->count(),
    ];

    return view('dashboard_individu', compact('individualSessions', 'stats'));
    }

    /**
     * Toggle status aktif/nonaktif sesi (untuk tombol saklar di tabel)
     */
    public function toggle(IndividualSession $session)
    {
        // Pastikan hanya pemilik sesi yang bisa mengubah status
        if ($session->user_id !== Auth::id()) {
            abort(403);
        }

        $session->update([
            'is_active' => !$session->is_active
        ]);

        $status = $session->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        return back()->with('success', "Sesi '{$session->title}' berhasil {$status}.");
    }
}
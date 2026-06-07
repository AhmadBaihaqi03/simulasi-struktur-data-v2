<?php

namespace App\Http\Controllers;

use App\Models\GroupAnswer;
use App\Models\PblGroupSession;
use Illuminate\Support\Facades\Auth;
//use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman utama dashboard instruktur.
     */
    public function index()
    {
        $user = Auth::user();

        // 1. Ambil data sesi untuk tabel (Perbaikan hitungan pending pada icon topi)
        $pblgroupsessions = PblGroupSession::where('user_id', $user->id)
        ->withCount([
            // Menghitung total kelompok yang sudah submit untuk kolom 'MURID'
            'groupAnswers as total_submitted_groups' => function($query) {
                $query->where('is_submitted', true);
            }, 
            // Menggunakan alias baru khusus untuk angka di icon topi sarjana
            'groupAnswers as real_pending_count' => function($query) {
                $query->where('is_submitted', true)
                      ->where(function($q) {
                          $q->whereDoesntHave('groupEvaluation')
                            ->orWhereHas('groupEvaluation', function($subQ) {
                                $subQ->whereNull('feedback_comment')
                                     ->orWhere('feedback_comment', '');
                            });
                      });
            }
        ])
        ->latest()
        ->paginate(10);

        // 2. Hitung statistik untuk 6 Card Utama (Perbaikan hitungan pending & graded)
        $stats = [
            'total'        => PblGroupSession::where('user_id', $user->id)->count(),
            'active'       => PblGroupSession::where('user_id', $user->id)->where('is_active', true)->count(),

            'total_groups' => GroupAnswer::where('is_submitted', true)
                            ->whereHas('pblGroupSession', function($q) use ($user) {
                                $q->where('user_id', $user->id);
                            })
                            ->count(),

            'pending'      => GroupAnswer::whereHas('pblGroupSession', function($q) use ($user) {
                                $q->where('user_id', $user->id);
                             })
                             ->where('is_submitted', true)
                             ->where(function($query) {
                                 $query->whereDoesntHave('groupEvaluation')
                                       ->orWhereHas('groupEvaluation', function($q) {
                                           $q->whereNull('feedback_comment')
                                             ->orWhere('feedback_comment', '');
                                       });
                             })
                             ->count(),

            'graded'       => GroupAnswer::whereHas('pblGroupSession', function($q) use ($user) {
                                $q->where('user_id', $user->id);
                             })
                             ->where('is_submitted', true)
                             ->whereHas('groupEvaluation', function($query) {
                                 $query->whereNotNull('feedback_comment')
                                       ->where('feedback_comment', '!=', '');
                             })
                             ->count(),
        ];

        return view('dashboard', compact('pblgroupsessions', 'stats'));
    }

    /**
     * Toggle status aktif/nonaktif sesi (untuk tombol saklar di tabel)
     */
    public function toggle(PblGroupSession $session)
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

    /**
     * Menghapus sesi beserta data terkait
     */
    public function destroy(PblGroupSession $session)
    {
        if ($session->user_id !== Auth::id()) {
            abort(403);
        }

        $session->delete();

        return back()->with('success', 'Sesi berhasil dihapus secara permanen.');
    }
}
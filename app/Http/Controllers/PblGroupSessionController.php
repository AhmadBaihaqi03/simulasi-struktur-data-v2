<?php

namespace App\Http\Controllers;

use App\Models\PblGroupSession;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PblGroupSessionController extends Controller
{
    public function create()
    {
        return view('sessions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'f3_questions' => 'nullable|array',
            'f5_questions' => 'nullable|array',
            'f1_learning_objectives' => 'nullable|array',
        ]);

        $request->user()->pblGroupSessions()->create([ 
            'session_code'   => strtoupper(Str::random(6)),
            'title'          => $request->title,
            'f1_context'     => $request->f1_context,
            'f3_questions'   => $request->f3_questions,
            'f5_questions'   => $request->f5_questions,
            'f1_learning_objectives' => $request->f1_learning_objectives,
            'f4_instruction' => $request->f4_instruction,
            'f4_question'    => $request->f4_question,
            'is_active'      => true,
        ]);

        return redirect()->route('dashboard')->with('success', 'Sesi berhasil dibuat!');
    }

    public function edit(PblGroupSession $session)
    {
        // Proteksi: Pastikan hanya pemilik yang bisa edit
        // if ($session->user_id !== auth()->id()) { abort(403); } -> sementara baris yang seperti ini aku komen dulu

        return view('sessions.edit', compact('session'));
    }

    public function update(Request $request, PblGroupSession $session)
    {
        //if ($session->user_id !== auth()->id()) { abort(403); } -> sementara baris yang seperti ini aku komen dulu

        $request->validate([
            'title' => 'required|string|max:255',
            'f3_questions' => 'nullable|array',
            'f5_questions' => 'nullable|array',
            'f1_learning_objectives' => 'nullable|array',
        ]);

        $session->update($request->all());

        return redirect()->route('dashboard')->with('success', 'Sesi diperbarui!');
    }

    public function toggle(PblGroupSession $session)
    {
        //if ($session->user_id !== auth()->id()) { abort(403); } -> sementara baris yang seperti ini aku komen dulu

        $session->update(['is_active' => !$session->is_active]);
        
        $status = $session->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Sesi berhasil $status!");
    }

    public function destroy(PblGroupSession $session)
    {
        //if ($session->user_id !== auth()->id()) { abort(403); } -> sementara baris yang seperti ini aku komen dulu

        $session->delete();
        return back()->with('success', 'Sesi berhasil dihapus!');
    }


    public function evaluations(PblGroupSession $session)
    {
        // Cukup ambil yang is_submitted saja
        $groups = $session->groupAnswers()
                        ->where('is_submitted', true)
                        ->get();

        return view('sessions.evaluation', compact('session', 'groups')); 
    }
}
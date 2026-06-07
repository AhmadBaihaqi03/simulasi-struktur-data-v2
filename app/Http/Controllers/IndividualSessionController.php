<?php

namespace App\Http\Controllers;

use App\Models\IndividualQuestion;
use App\Models\IndividualSubmission;
use App\Models\IndividualSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class IndividualSessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('sesi_individu.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'title'     => 'required|string|max:255',
            'questions' => 'required|array', // Memastikan data pertanyaan terkirim
            'questions.*.type' => 'required|string|in:multiple_choice,checkbox,drag_drop,grouping',
            'questions.*.question_text' => 'required|string',
            'questions.*.points' => 'required|integer|min:0',
        ]);

        // 2. Gunakan Transaction agar data tersimpan dengan aman
        DB::transaction(function () use ($request) {
            
            // Simpan Sesi Individu
            $session = IndividualSession::create([
                'user_id'     => Auth::id(),
                'title'       => $request->title,
                'access_code' => strtoupper(Str::random(6)),
                'is_active'   => true,
            ]);

            // 3. Simpan setiap baris pertanyaan ke dalam database
            foreach ($request->questions as $i => $q) {
                $type = $q['type'] ?? 'multiple_choice';
                $points = isset($q['points']) ? intval($q['points']) : 10;

                // Normalize options
                $options = [];
                if (isset($q['options']) && is_array($q['options'])) {
                    foreach ($q['options'] as $opt) {
                        if (is_array($opt)) {
                            $options[] = $opt;
                            continue;
                        }

                        $opt = is_null($opt) ? '' : trim((string) $opt);
                        if ($opt === '') {
                            continue;
                        }

                        $decoded = json_decode($opt, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $options = array_merge($options, $decoded);
                            continue;
                        }

                        $options[] = $opt;
                    }
                }

                // Determine correct answer format per type
                $correct = null;
                if ($type === 'multiple_choice') {
                    // radio sends questions[i][correct_index]
                    $correct = isset($q['correct_index']) ? intval($q['correct_index']) : 0;
                } elseif ($type === 'checkbox') {
                    // checkbox sends questions[i][correct_answer][] as indices
                    $correct = [];
                    if (isset($q['correct_answer']) && is_array($q['correct_answer'])) {
                        foreach ($q['correct_answer'] as $c) {
                            $correct[] = intval($c);
                        }
                    }
                } elseif ($type === 'drag_drop') {
                    // drag_drop may send a CSV/text in correct_answer or hidden inputs named correct_answer[]
                    if (isset($q['correct_answer'])) {
                        if (is_array($q['correct_answer'])) {
                            $correct = array_values($q['correct_answer']);
                        } elseif (is_string($q['correct_answer'])) {
                            $str = trim($q['correct_answer']);
                            if ($str === '') {
                                $correct = [];
                            } elseif (str_starts_with($str, '[')) {
                                $decoded = json_decode($str, true);
                                $correct = is_array($decoded) ? $decoded : array_map('trim', explode(',', $str));
                            } else {
                                $correct = array_map('trim', explode(',', $str));
                            }
                        } else {
                            $correct = [];
                        }
                    } else {
                        $correct = [];
                    }
                } elseif ($type === 'grouping') {
                    // grouping stores the mapping of item => group as JSON array of objects
                    if (isset($q['correct_answer'])) {
                        if (is_array($q['correct_answer'])) {
                            $correct = array_values($q['correct_answer']);
                        } elseif (is_string($q['correct_answer'])) {
                            $decoded = json_decode($q['correct_answer'], true);
                            $correct = json_last_error() === JSON_ERROR_NONE ? $decoded : [];
                        } else {
                            $correct = [];
                        }
                    } else {
                        $correct = [];
                    }
                } else {
                    $correct = $q['correct_answer'] ?? [];
                }

                $session->individualQuestions()->create([
                    'type'           => $type,
                    'question_text'  => $q['question_text'] ?? '',
                    'points'         => $points,
                    'options'        => $options,
                    'correct_answer' => $correct,
                ]);
            }
        });

        return redirect()->route('dashboard.individu')->with('success', 'Sesi berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(IndividualSession $sesi_individu)
    {
        $sesi_individu->load('individualQuestions');

        return view('sesi_individu.edit', ['individualSession' => $sesi_individu]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, IndividualSession $sesi_individu)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
            'questions' => 'required|array',
            'questions.*.type' => 'required|string|in:multiple_choice,checkbox,drag_drop,grouping',
            'questions.*.question_text' => 'required|string',
            'questions.*.points' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($request, $sesi_individu) {
            $sesi_individu->update([
                'title' => $request->title,
                'is_active' => $request->boolean('is_active'),
            ]);

            $sesi_individu->individualQuestions()->delete();

            foreach ($request->questions as $q) {
                $type = $q['type'] ?? 'multiple_choice';
                $points = isset($q['points']) ? intval($q['points']) : 10;

                $options = [];
                if (isset($q['options']) && is_array($q['options'])) {
                    foreach ($q['options'] as $opt) {
                        if (is_array($opt)) {
                            $options[] = $opt;
                            continue;
                        }

                        $opt = is_null($opt) ? '' : trim((string) $opt);
                        if ($opt === '') {
                            continue;
                        }

                        $decoded = json_decode($opt, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $options = array_merge($options, $decoded);
                            continue;
                        }

                        $options[] = $opt;
                    }
                }

                $correct = null;
                if ($type === 'multiple_choice') {
                    $correct = isset($q['correct_index']) ? intval($q['correct_index']) : 0;
                } elseif ($type === 'checkbox') {
                    $correct = [];
                    if (isset($q['correct_answer']) && is_array($q['correct_answer'])) {
                        foreach ($q['correct_answer'] as $c) {
                            $correct[] = intval($c);
                        }
                    }
                } elseif ($type === 'drag_drop') {
                    if (isset($q['correct_answer'])) {
                        if (is_array($q['correct_answer'])) {
                            $correct = array_values($q['correct_answer']);
                        } elseif (is_string($q['correct_answer'])) {
                            $str = trim($q['correct_answer']);
                            if ($str === '') {
                                $correct = [];
                            } elseif (str_starts_with($str, '[')) {
                                $decoded = json_decode($str, true);
                                $correct = is_array($decoded) ? $decoded : array_map('trim', explode(',', $str));
                            } else {
                                $correct = array_map('trim', explode(',', $str));
                            }
                        } else {
                            $correct = [];
                        }
                    } else {
                        $correct = [];
                    }
                } elseif ($type === 'grouping') {
                    if (isset($q['correct_answer'])) {
                        if (is_array($q['correct_answer'])) {
                            $correct = array_values($q['correct_answer']);
                        } elseif (is_string($q['correct_answer'])) {
                            $decoded = json_decode($q['correct_answer'], true);
                            $correct = json_last_error() === JSON_ERROR_NONE ? $decoded : [];
                        } else {
                            $correct = [];
                        }
                    } else {
                        $correct = [];
                    }
                } else {
                    $correct = $q['correct_answer'] ?? [];
                }

                $sesi_individu->individualQuestions()->create([
                    'type' => $type,
                    'question_text' => $q['question_text'] ?? '',
                    'points' => $points,
                    'options' => $options,
                    'correct_answer' => $correct,
                ]);
            }
        });

        return redirect()->route('dashboard.individu')->with('success', 'Sesi berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(IndividualSession $sesi_individu)
    {
        $sesi_individu->delete();
        return back()->with('success', 'Sesi berhasil dihapus!');
    }

    public function showSubmissions(IndividualSession $session)
    {
        if ($session->user_id !== Auth::id()) {
            abort(403);
        }

        $session->load([
            'individualSubmissions' => function ($query) {
                $query->withCount('individualAnswers')
                    ->latest();
            },
            'individualQuestions',
        ]);

        return view('sesi_individu.nilai', compact('session'));
    }

    public function reviewSubmission(IndividualSubmission $submission)
    {
        $submission->load(['individualSession.individualQuestions', 'individualAnswers.individualQuestion']);

        if ($submission->individualSession->user_id !== Auth::id()) {
            abort(403);
        }

        return view('sesi_individu.nilai_detail', compact('submission'));
    }

    public function toggle(IndividualSession $session)
    {
        //if ($session->user_id !== auth()->id()) { abort(403); } -> sementara baris yang seperti ini aku komen dulu

        $session->update(['is_active' => !$session->is_active]);
        
        $status = $session->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Sesi berhasil $status!");
    }
}

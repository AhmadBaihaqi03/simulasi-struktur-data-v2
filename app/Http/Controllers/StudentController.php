<?php

namespace App\Http\Controllers;

use App\Models\IndividualAnswer;
use App\Models\IndividualQuestion;
use App\Models\IndividualSession;
use App\Models\IndividualSubmission;
use App\Models\PblGroupSession;
use App\Models\GroupAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentController extends Controller
{
    // Validasi Kode Sesi dari Guru
    public function checkSession(Request $request)
    {
        $request->validate([
            'session_code' => 'required|string',
        ]);

        $session = PblGroupSession::where('session_code', strtoupper($request->session_code))
            ->where('is_active', true)
            ->first();

        if (!$session) {
            return back()->with('error', 'Kode sesi tidak valid atau sudah dinonaktifkan oleh Guru.');
        }

        return redirect()->route('student.orientasi', $session->session_code);
    }

    // Validasi Kode Sesi Individu dari Guru
    public function checkIndividualSession(Request $request)
    {
        $request->validate([
            'session_code' => 'required|string',
        ]);

        $session = IndividualSession::where('access_code', strtoupper($request->session_code))
            ->where('is_active', true)
            ->first();

        if (!$session) {
            return back()->with('individual_error', 'Kode sesi individu tidak valid atau sudah dinonaktifkan oleh Guru.');
        }

        return redirect()->route('student.individual.quiz', $session->access_code);
    }

    // Halaman kuis individu
    public function showIndividualQuiz(string $session_code)
    {
        $session = IndividualSession::with('individualQuestions')
            ->where('access_code', strtoupper($session_code))
            ->where('is_active', true)
            ->firstOrFail();

        return view('student.individual_quiz', compact('session'));
    }

    // Submit kuis individu dan hitung nilai otomatis
    public function submitIndividualQuiz(Request $request, string $session_code)
    {
        $request->validate([
            'student_name' => 'required|string|max:255',
            'class_name' => 'required|string|max:255',
            'student_number' => 'required|string|max:100',
            'answers' => 'required|array',
        ]);

        $session = IndividualSession::with('individualQuestions')
            ->where('access_code', strtoupper($session_code))
            ->where('is_active', true)
            ->firstOrFail();

        $result = DB::transaction(function () use ($request, $session) {
            $submission = IndividualSubmission::create([
                'individual_session_id' => $session->id,
                'student_name' => trim($request->student_name),
                'student_number' => trim($request->student_number),
                'class_name' => trim($request->class_name),
                'total_score' => 0,
            ]);

            $totalScore = 0;

            foreach ($session->individualQuestions as $question) {
                $submittedAnswer = $request->input('answers.' . $question->id);
                $graded = $this->gradeIndividualQuestion($question, $submittedAnswer);

                $submission->individualAnswers()->create([
                    'individual_question_id' => $question->id,
                    'answer_given' => $graded['answer_given'],
                    'is_correct' => $graded['is_correct'],
                    'score_earned' => $graded['score_earned'],
                ]);

                $totalScore += $graded['score_earned'];
            }

            $submission->update([
                'total_score' => $totalScore,
            ]);

            return $submission->fresh(['individualAnswers.individualQuestion', 'individualSession.individualQuestions']);
        });

        return redirect()->route('student.individual.result', $result)->with('success', 'Kuis berhasil dikumpulkan. Nilai sudah dihitung otomatis.');
    }

    // Halaman hasil kuis individu
    public function showIndividualResult(IndividualSubmission $submission)
    {
        $submission->load(['individualSession', 'individualAnswers.individualQuestion']);

        return view('student.individual_result', compact('submission'));
    }

    // Download PDF hasil kuis individu
    public function downloadIndividualResult(IndividualSubmission $submission)
    {
        $submission->load(['individualSession', 'individualAnswers.individualQuestion']);

        $pdf = Pdf::loadView('student.individual_result_pdf', compact('submission'))
            ->setPaper('a4', 'portrait');

        $filename = 'hasil-kuis-individu-' . Str::slug($submission->student_name) . '-' . $submission->id . '.pdf';

        return $pdf->download($filename);
    }

    // Halaman orientasi
    public function showOrientasi($session_code)
    {
        $session = PblGroupSession::where('session_code', $session_code)
            ->where('is_active', true)
            ->firstOrFail();

        return view('student.orientasi', compact('session'));
    }

    // Join / Create Group
    public function joinGroup(Request $request, $session_code)
    {
        $session = PblGroupSession::where('session_code', $session_code)->firstOrFail();

        $request->validate([
            'group_name' => 'required|string|max:100',
        ]);

        $group = GroupAnswer::firstOrCreate(
            [
                'pbl_group_session_id' => $session->id,
                'group_name' => strtoupper($request->group_name)
            ],
            [
                'student_data' => [],
            ]
        );

        if ($group->is_submitted) {
            return redirect()->route('student.complete', [$session->session_code, $group->id]);
        }

        // LANGSUNG KE WORKSPACE (tanpa phase)
        return redirect()->route('student.workspace', [
            'session_code' => $session->session_code,
            'group_id' => $group->id,
        ]);
    }

    // Workspace utama
    public function showWorkspace($session_code, $group_id)
    {
        $session = PblGroupSession::where('session_code', $session_code)->firstOrFail();

        $group = GroupAnswer::where('id', $group_id)
            ->where('pbl_group_session_id', $session->id)
            ->firstOrFail();

        if ($group->is_submitted) {
            return redirect()->route('student.complete', [$session_code, $group->id]);
        }

        return view('student.workspace', compact('session', 'group'));
    }

    // Save & Submit
    public function saveAll(Request $request, $session_code, $group_id)
    {
        $group = GroupAnswer::findOrFail($group_id);

        $inputMembers = collect($request->members)
            ->filter()
            ->map(fn($n) => trim($n));

        if ($inputMembers->count() !== $inputMembers->unique()->count()) {
            return back()->with('error', 'Ada nama anggota yang ganda di inputan kelompok Anda!')->withInput();
        }

        $teacherId = $group->pblGroupSession()->first()->user_id;

        foreach ($inputMembers as $name) {
            $exists = GroupAnswer::where('id', '!=', $group_id)
                ->whereHas('pblGroupSession', function($query) use ($teacherId) {
                    $query->where('user_id', $teacherId);
                })
                ->where('student_data->members', 'LIKE', '%"' . $name . '"%')
                ->exists();

            if ($exists) {
                return back()->with('error', "Nama '$name' sudah terdaftar di kelas lain milik guru ini!")->withInput();
            }
        }

        $group->update([
            'class_name' => $request->class_name,
            'f3_answers' => $request->f3_answers,
            'f4_link' => $request->f4_link,
            'f4_answers' => $request->f4_answers,
            'f5_answers' => $request->f5_answers,
            'student_data' => [
                'members' => $request->members,
            ],
        ]);

        if ($request->action == 'submit') {
            $group->update([
                'is_submitted' => true
            ]);

            return redirect()->route('student.complete', [
                $session_code,
                $group->id
            ])->with('success', 'Tugas dikirim!');
        }

        return back()->with('success', 'Progres berhasil disimpan!');
    }

    // Live check
    public function checkMemberName(Request $request)
    {
        $name = trim($request->name);
        $groupId = $request->group_id;

        $group = GroupAnswer::with('pblGroupSession')->find($groupId);

        if (!$group || !$group->pblGroupSession) {
            return response()->json(['exists' => false]);
        }

        $teacherId = $group->pblGroupSession->user_id;

        $exists = GroupAnswer::where('id', '!=', $groupId)
            ->whereHas('pblGroupSession', function($query) use ($teacherId) {
                $query->where('user_id', $teacherId);
            })
            ->where('student_data->members', 'LIKE', '%"' . $name . '"%')
            ->exists();

        return response()->json(['exists' => $exists]);
    }

    // Complete page
    public function complete($session_code, $group_id)
    {
        $session = PblGroupSession::where('session_code', $session_code)->firstOrFail();

        $group = GroupAnswer::with('GroupEvaluation')->findOrFail($group_id);

        if (!$group->is_submitted) {
            return redirect()->route('student.workspace', [$session_code, $group->id]);
        }

        return view('student.viewfeedback', compact('session', 'group'));
    }

    private function gradeIndividualQuestion(IndividualQuestion $question, mixed $submittedAnswer): array
    {
        $points = (int) $question->points;

        if ($question->type === 'multiple_choice') {
            $selectedIndex = is_null($submittedAnswer) || $submittedAnswer === '' ? null : (int) $submittedAnswer;
            $correctIndex = is_array($question->correct_answer) ? (int) ($question->correct_answer[0] ?? 0) : (int) $question->correct_answer;
            $selectedText = $selectedIndex !== null ? ($question->options[$selectedIndex] ?? null) : null;
            $isCorrect = $selectedIndex !== null && $selectedIndex === $correctIndex;

            return [
                'answer_given' => [
                    'selected_index' => $selectedIndex,
                    'selected_text' => $selectedText,
                ],
                'is_correct' => $isCorrect,
                'score_earned' => $isCorrect ? $points : 0,
            ];
        }

        if ($question->type === 'checkbox') {
            $selectedIndices = collect(is_array($submittedAnswer) ? $submittedAnswer : [])
                ->map(fn ($value) => (int) $value)
                ->sort()
                ->values()
                ->all();

            $correctIndices = collect($question->correct_answer ?? [])
                ->map(fn ($value) => (int) $value)
                ->sort()
                ->values()
                ->all();

            $selectedTexts = array_map(function ($index) use ($question) {
                return $question->options[$index] ?? null;
            }, $selectedIndices);

            $isCorrect = $selectedIndices === $correctIndices;

            return [
                'answer_given' => [
                    'selected_indices' => $selectedIndices,
                    'selected_texts' => $selectedTexts,
                ],
                'is_correct' => $isCorrect,
                'score_earned' => $isCorrect ? $points : 0,
            ];
        }

        if ($question->type === 'drag_drop') {
            $submittedItems = is_array($submittedAnswer)
                ? $submittedAnswer
                : array_map('trim', explode(',', (string) $submittedAnswer));

            $submittedItems = array_values(array_filter($submittedItems, fn ($value) => $value !== ''));
            $correctItems = array_map(fn ($value) => trim((string) $value), is_array($question->correct_answer) ? $question->correct_answer : []);

            $isCorrect = $submittedItems === $correctItems;

            return [
                'answer_given' => [
                    'ordered_items' => $submittedItems,
                ],
                'is_correct' => $isCorrect,
                'score_earned' => $isCorrect ? $points : 0,
            ];
        }

        if ($question->type === 'grouping') {
            $submittedGroups = $this->normalizeGroupingSubmission($submittedAnswer);
            $correctPairs = collect($question->correct_answer ?? []);

            $correctMap = $correctPairs->mapWithKeys(function ($row) {
                $item = mb_strtolower(trim((string) ($row['item'] ?? '')));
                $group = mb_strtolower(trim((string) ($row['group'] ?? '')));

                return [$item => $group];
            })->all();

            $givenDetails = [];
            $correctCount = 0;
            $totalItems = count($correctMap);

            foreach ($submittedGroups as $row) {
                $item = trim((string) ($row['item'] ?? ''));
                $group = trim((string) ($row['group'] ?? ''));
                $normalizedItem = mb_strtolower($item);
                $normalizedGroup = mb_strtolower($group);
                $expectedGroup = $correctMap[$normalizedItem] ?? null;
                $isCorrectRow = $expectedGroup !== null && $expectedGroup === $normalizedGroup;

                if ($isCorrectRow) {
                    $correctCount++;
                }

                $givenDetails[] = [
                    'item' => $item,
                    'group' => $group,
                    'is_correct' => $isCorrectRow,
                ];
            }

            $scorePerItem = $totalItems > 0 ? ($points / $totalItems) : 0;
            $earnedScore = (int) round($scorePerItem * $correctCount);

            return [
                'answer_given' => [
                    'pairs' => $givenDetails,
                ],
                'is_correct' => $totalItems > 0 && $correctCount === $totalItems,
                'score_earned' => $earnedScore,
            ];
        }

        return [
            'answer_given' => $submittedAnswer,
            'is_correct' => false,
            'score_earned' => 0,
        ];
    }

    private function normalizeGroupingSubmission(mixed $submittedAnswer): array
    {
        if (!is_array($submittedAnswer)) {
            return [];
        }

        $items = $submittedAnswer['item'] ?? [];
        $groups = $submittedAnswer['group'] ?? [];
        $pairs = [];

        foreach ($items as $index => $item) {
            $itemValue = trim((string) $item);
            if ($itemValue === '') {
                continue;
            }

            $pairs[] = [
                'item' => $itemValue,
                'group' => trim((string) ($groups[$index] ?? '')),
            ];
        }

        return $pairs;
    }
}
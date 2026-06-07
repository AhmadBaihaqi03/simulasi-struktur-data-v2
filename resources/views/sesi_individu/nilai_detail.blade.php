<x-app-layout>
    <style>
        body { background-color: #f8f9fa; }
        .card-soft { border: none; border-radius: 18px; }
        .pill { border-radius: 999px; padding: 0.35rem 0.7rem; font-size: 0.75rem; font-weight: 700; }
    </style>

    <div class="container py-4 py-md-5">
        <div class="mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <div class="text-uppercase small fw-bold text-muted">Review Jawaban</div>
                <h1 class="fw-bold mb-1">{{ $submission->student_name }}</h1>
                <p class="text-muted mb-0">
                    {{ $submission->individualSession->title }} · Kelas {{ $submission->class_name }} · Absen {{ $submission->student_number }}
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('sesi-individu.nilai', $submission->individualSession) }}" class="btn btn-light border">Kembali</a>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card card-soft shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small fw-bold text-uppercase">Nilai Akhir</div>
                        <div class="display-6 fw-bold text-indigo">{{ $submission->total_score }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-soft shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small fw-bold text-uppercase">Jumlah Soal</div>
                        <div class="display-6 fw-bold text-indigo">{{ $submission->individualSession->individualQuestions->count() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-soft shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small fw-bold text-uppercase">Jawaban Benar</div>
                        <div class="display-6 fw-bold text-indigo">
                            {{ $submission->individualAnswers->where('is_correct', true)->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-3">
            @foreach($submission->individualAnswers as $answer)
                @php
                    $question = $answer->individualQuestion;
                    $given = $answer->answer_given ?? [];
                    $isCorrect = (bool) $answer->is_correct;
                    $correctAnswer = $question?->correct_answer ?? [];
                @endphp
                <div class="card card-soft shadow-sm mb-3">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                            <div>
                                <div class="text-uppercase small fw-bold text-muted">Soal</div>
                                <h5 class="fw-bold mb-1">{{ $question?->question_text }}</h5>
                                <div class="text-muted small">Tipe: {{ str_replace('_', ' ', $question?->type ?? '-') }} · Poin: {{ $question?->points ?? 0 }}</div>
                            </div>
                            <span class="pill {{ $isCorrect ? 'bg-success-subtle text-success border border-success' : 'bg-danger-subtle text-danger border border-danger' }}">
                                {{ $isCorrect ? 'Benar' : 'Salah' }} · {{ $answer->score_earned }} poin
                            </span>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="p-3 rounded-3 bg-light border h-100">
                                    <div class="text-uppercase small fw-bold text-muted mb-2">Jawaban Murid</div>
                                    @if($question?->type === 'multiple_choice')
                                        <div class="fw-semibold">
                                            {{ $question->options[$given['selected_index'] ?? -1] ?? '-' }}
                                        </div>
                                    @elseif($question?->type === 'checkbox')
                                        <div class="fw-semibold">
                                            {{ collect($given['selected_texts'] ?? [])->filter()->implode(', ') ?: '-' }}
                                        </div>
                                    @elseif($question?->type === 'drag_drop')
                                        <div class="fw-semibold">
                                            {{ collect($given['ordered_items'] ?? [])->filter()->implode(' → ') ?: '-' }}
                                        </div>
                                    @elseif($question?->type === 'grouping')
                                        @if(!empty($given['pairs']))
                                            <ul class="mb-0 ps-3">
                                                @foreach($given['pairs'] as $row)
                                                    <li>{{ $row['item'] ?? '-' }} → {{ $row['group'] ?: '-' }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <div class="fw-semibold">-</div>
                                        @endif
                                    @else
                                        <div class="fw-semibold">{{ is_array($given) ? json_encode($given) : ($given ?: '-') }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 rounded-3 bg-light border h-100">
                                    <div class="text-uppercase small fw-bold text-muted mb-2">Kunci Jawaban</div>
                                    @if($question?->type === 'multiple_choice')
                                        <div class="fw-semibold">
                                            {{ $question->options[(int) ($correctAnswer[0] ?? $correctAnswer ?? 0)] ?? '-' }}
                                        </div>
                                    @elseif($question?->type === 'checkbox')
                                        <div class="fw-semibold">
                                            {{ collect($correctAnswer ?? [])->map(fn ($idx) => $question->options[(int) $idx] ?? null)->filter()->implode(', ') ?: '-' }}
                                        </div>
                                    @elseif($question?->type === 'drag_drop')
                                        <div class="fw-semibold">
                                            {{ collect($correctAnswer ?? [])->filter()->implode(' → ') ?: '-' }}
                                        </div>
                                    @elseif($question?->type === 'grouping')
                                        @if(!empty($correctAnswer))
                                            <ul class="mb-0 ps-3">
                                                @foreach($correctAnswer as $row)
                                                    <li>{{ $row['item'] ?? '-' }} → {{ $row['group'] ?? '-' }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <div class="fw-semibold">-</div>
                                        @endif
                                    @else
                                        <div class="fw-semibold">{{ is_array($correctAnswer) ? json_encode($correctAnswer) : ($correctAnswer ?: '-') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>

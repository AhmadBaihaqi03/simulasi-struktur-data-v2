<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111827;
        }
        .header {
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            color: #111827;
        }
        .muted {
            color: #6B7280;
            font-size: 11px;
        }
        .card {
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 12px;
        }
        .question-title {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .meta {
            font-size: 10px;
            color: #4B5563;
            margin-bottom: 8px;
        }
        .status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status.ok { background: #DCFCE7; color: #166534; }
        .status.bad { background: #FEE2E2; color: #B91C1C; }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Hasil Kuis Individu</div>
        <div class="muted">{{ $submission->individualSession->title }} | Kode: {{ $submission->individualSession->access_code }}</div>
    </div>

    <div class="card">
        <table>
            <tr>
                <td><strong>Nama</strong><br>{{ $submission->student_name }}</td>
                <td><strong>Kelas</strong><br>{{ $submission->class_name }}</td>
                <td><strong>No Absen</strong><br>{{ $submission->student_number }}</td>
                <td><strong>Skor Total</strong><br>{{ $submission->total_score }}</td>
            </tr>
        </table>
    </div>

    @foreach($submission->individualAnswers as $answer)
        @php
            $question = $answer->individualQuestion;
            $given = $answer->answer_given;
        @endphp
        <div class="card">
            <div class="question-title">{{ $loop->iteration }}. {{ $question->question_text }}</div>
            <div class="meta">Tipe: {{ str_replace('_', ' ', strtoupper($question->type)) }} | Poin: {{ $answer->score_earned }} / {{ $question->points }}</div>
            <div class="status {{ $answer->is_correct ? 'ok' : 'bad' }}">{{ $answer->is_correct ? 'Benar' : 'Salah' }}</div>
            <div style="margin-top:10px;">
                @if($question->type === 'multiple_choice')
                    Jawaban: {{ $given['selected_text'] ?? '-' }}
                @elseif($question->type === 'checkbox')
                    Jawaban: {{ implode(', ', $given['selected_texts'] ?? []) ?: '-' }}
                @elseif($question->type === 'drag_drop')
                    Jawaban: {{ implode(' → ', $given['ordered_items'] ?? []) ?: '-' }}
                @elseif($question->type === 'grouping')
                    <table>
                        @forelse(($given['pairs'] ?? []) as $pair)
                            <tr>
                                <td style="width:60%; padding:4px 0;">{{ $pair['item'] }}</td>
                                <td style="width:25%; padding:4px 0;">{{ $pair['group'] ?: '-' }}</td>
                                <td style="width:15%; padding:4px 0;">{{ ($pair['is_correct'] ?? false) ? 'Benar' : 'Salah' }}</td>
                            </tr>
                        @empty
                            <tr><td>-</td></tr>
                        @endforelse
                    </table>
                @endif
            </div>
        </div>
    @endforeach
</body>
</html>

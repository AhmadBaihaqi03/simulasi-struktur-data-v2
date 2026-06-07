@extends('layouts.student')

@section('workspace_title')
    <div class="flex items-center gap-2 flex-wrap">
        <span class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Hasil Kuis Individu | Kode:</span>
        <span class="text-[11px] font-black text-indigo-600 tracking-widest uppercase italic">{{ $submission->individualSession->access_code }}</span>
    </div>

    <h1 class="text-2xl sm:text-3xl md:text-5xl font-black text-slate-900 tracking-tight leading-tight mt-2">
        Sesi: {{ $submission->individualSession->title }}
    </h1>
@endsection

@section('content')
    <div class="flex flex-col lg:grid lg:grid-cols-12 gap-6 lg:gap-8">
        <div class="lg:col-span-4 order-first lg:order-last">
            <div class="sticky top-8 space-y-4 sm:space-y-6">
                <div class="bg-white rounded-[2rem] border border-indigo-50 p-5 sm:p-8 shadow-xl shadow-indigo-100/20">
                    <div class="text-center pb-4 sm:pb-6 border-b border-slate-50">
                        <h6 class="text-[11px] font-black text-slate-900 tracking-[0.2em] uppercase mb-2">Ringkasan Nilai</h6>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-center">
                            <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Skor Total</div>
                            <div class="text-3xl font-black text-indigo-600">{{ $submission->total_score }}</div>
                        </div>
                    </div>

                    <div class="mt-6 space-y-2 text-sm text-slate-600">
                        <p><span class="font-bold text-slate-900">Nama:</span> {{ $submission->student_name }}</p>
                        <p><span class="font-bold text-slate-900">Kelas:</span> {{ $submission->class_name }}</p>
                        <p><span class="font-bold text-slate-900">No Absen:</span> {{ $submission->student_number }}</p>
                    </div>

                    <div class="mt-6 space-y-3">
                        <a href="{{ route('student.individual.download', $submission) }}" class="flex items-center justify-center gap-3 bg-indigo-600 text-white py-4 rounded-2xl font-black text-xs tracking-widest hover:bg-slate-900 shadow-lg shadow-indigo-100 transition-all active:scale-[0.98] group min-h-[52px]">
                            <i data-lucide="download" class="w-4 h-4 transition-transform group-hover:-translate-y-1"></i> UNDUH PDF
                        </a>
                        <a href="{{ route('beranda') }}" class="flex items-center justify-center gap-3 bg-white text-slate-700 py-4 rounded-2xl font-black text-xs tracking-widest border border-slate-200 hover:bg-slate-50 transition-all min-h-[52px]">
                            <i data-lucide="home" class="w-4 h-4"></i> KEMBALI KE BERANDA
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-8 order-last lg:order-first space-y-6 sm:space-y-8">
            @foreach($submission->individualAnswers as $answer)
                @php
                    $question = $answer->individualQuestion;
                    $given = $answer->answer_given;
                @endphp
                <div class="bg-white rounded-[2rem] border border-indigo-50 p-4 sm:p-8 md:p-10 shadow-sm">
                    <div class="flex items-start justify-between gap-4 mb-5 sm:mb-6">
                        <div class="flex items-center gap-3 sm:gap-4">
                            <div class="bg-indigo-600 text-white rounded-xl sm:rounded-2xl p-2.5 sm:p-3 shadow-lg shadow-indigo-100 shrink-0">
                                <span class="text-xs font-black">{{ $loop->iteration }}</span>
                            </div>
                            <div>
                                <h2 class="text-base sm:text-xl font-black text-slate-900 tracking-tight leading-tight">{{ $question->question_text }}</h2>
                                <p class="text-[10px] sm:text-xs font-black text-indigo-500 uppercase tracking-[0.2em] mt-1">{{ str_replace('_', ' ', strtoupper($question->type)) }} • {{ $answer->score_earned }} / {{ $question->points }} Poin</p>
                            </div>
                        </div>
                        <span class="px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest {{ $answer->is_correct ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                            {{ $answer->is_correct ? 'Benar' : 'Salah' }}
                        </span>
                    </div>

                    <div class="p-4 sm:p-6 bg-slate-50 border border-slate-100 rounded-2xl">
                        @if($question->type === 'multiple_choice')
                            <div class="text-sm text-slate-700 font-semibold">Jawaban dipilih: <span class="text-slate-900">{{ $given['selected_text'] ?? '-' }}</span></div>
                        @elseif($question->type === 'checkbox')
                            <div class="text-sm text-slate-700 font-semibold mb-2">Jawaban dipilih:</div>
                            <ul class="list-disc ml-5 space-y-1 text-sm text-slate-700">
                                @forelse(($given['selected_texts'] ?? []) as $item)
                                    <li>{{ $item }}</li>
                                @empty
                                    <li>-</li>
                                @endforelse
                            </ul>
                        @elseif($question->type === 'drag_drop')
                            <div class="text-sm text-slate-700 font-semibold mb-2">Urutan jawaban:</div>
                            <p class="text-sm text-slate-700">{{ implode(' → ', $given['ordered_items'] ?? []) ?: '-' }}</p>
                        @elseif($question->type === 'grouping')
                            <div class="space-y-2">
                                @forelse(($given['pairs'] ?? []) as $pair)
                                    <div class="flex items-center justify-between gap-3 p-3 rounded-xl border {{ ($pair['is_correct'] ?? false) ? 'border-emerald-200 bg-emerald-50' : 'border-rose-200 bg-rose-50' }}">
                                        <div class="text-sm font-semibold text-slate-700">
                                            {{ $pair['item'] }}
                                        </div>
                                        <div class="text-sm font-bold {{ ($pair['is_correct'] ?? false) ? 'text-emerald-700' : 'text-rose-700' }}">
                                            {{ $pair['group'] ?: '-' }}
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-sm text-slate-500">Belum ada jawaban kelompok.</div>
                                @endforelse
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

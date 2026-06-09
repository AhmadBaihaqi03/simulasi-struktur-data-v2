@extends('layouts.student')

@section('workspace_title')
    <div class="flex items-center gap-2 flex-wrap">
        <span class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Sesi Individu | Kode:</span>
        <span class="text-[11px] font-black text-indigo-600 tracking-widest uppercase italic">{{ $session->access_code }}</span>
    </div>

    <h1 class="text-2xl sm:text-3xl md:text-5xl font-black text-slate-900 tracking-tight leading-tight mt-2">
        Nama Sesi: {{ $session->title }}
    </h1>
@endsection

@section('content')
    <form action="{{ route('student.individual.submit', $session->access_code) }}" method="POST" class="space-y-6 sm:space-y-8">
        @csrf

        <div class="bg-slate-50 border border-slate-100 rounded-[2rem] p-4 sm:p-8 md:p-10 shadow-sm">
            <div class="flex items-center gap-3 sm:gap-4 mb-6 sm:mb-8">
                <div class="bg-indigo-600 text-white rounded-xl sm:rounded-2xl p-2.5 sm:p-3 shadow-lg shadow-indigo-100 shrink-0">
                    <i data-lucide="id-card" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                </div>
                <h2 class="text-base sm:text-xl font-black text-slate-900 tracking-tight uppercase leading-tight">Identitas Murid</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-5">
                <div>
                    <label class="block text-[11px] font-black text-indigo-500 uppercase tracking-[0.2em] mb-2">Nama</label>
                    <input type="text" name="student_name" value="{{ old('student_name') }}" class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 font-semibold text-slate-700 min-h-[44px]" required>
                </div>
                <div>
                    <label class="block text-[11px] font-black text-indigo-500 uppercase tracking-[0.2em] mb-2">Kelas</label>
                    <input type="text" name="class_name" value="{{ old('class_name') }}" class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 font-semibold text-slate-700 min-h-[44px]" placeholder="Contoh: X TKJ 1" required>
                </div>
                <div>
                    <label class="block text-[11px] font-black text-indigo-500 uppercase tracking-[0.2em] mb-2">No. Absen</label>
                    <input type="text" name="student_number" value="{{ old('student_number') }}" class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 font-semibold text-slate-700 min-h-[44px]" required>
                </div>
            </div>
        </div>

        @if($session->individualQuestions->isEmpty())
            <div class="bg-red-50 border border-red-100 rounded-[2rem] p-6 text-red-700 font-semibold">
                Sesi ini belum memiliki soal.
            </div>
        @else
            <div class="space-y-6 sm:space-y-8">
                @foreach($session->individualQuestions as $question)
                    <div class="bg-white rounded-[2rem] border border-indigo-50 p-4 sm:p-8 md:p-10 shadow-sm">
                        <div class="flex items-start justify-between gap-4 mb-5 sm:mb-6">
                            <div class="flex items-center gap-3 sm:gap-4">
                                <div class="bg-indigo-600 text-white rounded-xl sm:rounded-2xl p-2.5 sm:p-3 shadow-lg shadow-indigo-100 shrink-0">
                                    <span class="text-xs font-black">{{ $loop->iteration }}</span>
                                </div>
                                <div>
                                    <h2 class="text-base sm:text-xl font-black text-slate-900 tracking-tight leading-tight">{{ $question->question_text }}</h2>
                                    <p class="text-[10px] sm:text-xs font-black text-indigo-500 uppercase tracking-[0.2em] mt-1">{{ str_replace('_', ' ', strtoupper($question->type)) }} • {{ $question->points }} Poin</p>
                                </div>
                            </div>
                        </div>

                        @if($question->type === 'multiple_choice')
                            <div class="space-y-3">
                                @foreach($question->options ?? [] as $index => $option)
                                    <label class="flex items-start gap-3 p-4 rounded-2xl border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/40 transition cursor-pointer">
                                        <input type="radio" name="answers[{{ $question->id }}]" value="{{ $index }}" class="mt-1 accent-indigo-600" required>
                                        <span class="text-slate-700 font-medium">{{ $option }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @elseif($question->type === 'checkbox')
                            <div class="space-y-3">
                                @foreach($question->options ?? [] as $index => $option)
                                    <label class="flex items-start gap-3 p-4 rounded-2xl border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/40 transition cursor-pointer">
                                        <input type="checkbox" name="answers[{{ $question->id }}][]" value="{{ $index }}" class="mt-1 accent-indigo-600">
                                        <span class="text-slate-700 font-medium">{{ $option }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @elseif($question->type === 'drag_drop')
                            @php
                                $dragOptions = is_array($question->options) ? $question->options : [];
                                $shuffledDragOptions = $dragOptions;
                                shuffle($shuffledDragOptions);
                            @endphp

                            <div class="space-y-3">
                                <label class="block text-[11px] font-black text-indigo-500 uppercase tracking-[0.2em] mb-2">Susun urutan jawaban</label>
                                <div class="drag-drop-container" data-question-id="{{ $question->id }}">
                                    <ul class="dd-list space-y-2">
                                        @foreach($shuffledDragOptions as $option)
                                            <li class="dd-item flex items-center gap-3 p-3 bg-white rounded-2xl border border-slate-200" draggable="true">
                                                <div class="flex gap-2 items-center">
                                                    <button type="button" class="btn-up px-2 py-1 text-xs bg-slate-100 rounded">↑</button>
                                                    <button type="button" class="btn-down px-2 py-1 text-xs bg-slate-100 rounded">↓</button>
                                                </div>
                                                <span class="dd-label text-slate-700 font-medium">{{ $option }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <input type="hidden" name="answers[{{ $question->id }}]" class="dd-hidden-input" required>
                                    <p class="text-xs text-slate-500 mt-2">Susun item dengan tombol atas/bawah atau seret ke posisi yang diinginkan.</p>
                                </div>
                            </div>
                        @elseif($question->type === 'grouping')
                            @php
                                $groupNames = is_array($question->options) ? $question->options : [];
                                $groupItems = is_array($question->correct_answer) ? $question->correct_answer : [];
                                $shuffledGroupItems = $groupItems;
                                shuffle($shuffledGroupItems);
                            @endphp

                            <div class="space-y-4 grouping-container" data-question-id="{{ $question->id }}">
                                <div class="p-3 bg-white rounded-2xl border border-slate-200">
                                    <div class="flex items-center justify-between gap-3 mb-3">
                                        <div>
                                            <h4 class="font-bold text-slate-700">Bank Item</h4>
                                            <p class="text-xs text-slate-500">Semua opsi jawaban ada di sini dulu, lalu seret ke kelompok yang sesuai.</p>
                                        </div>
                                    </div>
                                    <div class="group-dropzone min-h-[100px] flex flex-wrap gap-2" data-zone="bank">
                                        @foreach($shuffledGroupItems as $itemRow)
                                            <div class="group-item px-3 py-2 bg-slate-50 rounded-xl border border-slate-200 cursor-move" draggable="true" data-item="{{ $itemRow['item'] }}">
                                                {{ $itemRow['item'] }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-{{ max(1, count($groupNames)) }} gap-4">
                                    @foreach($groupNames as $groupName)
                                        <div class="group-column p-3 bg-white rounded-2xl border border-slate-200" data-group-name="{{ $groupName }}">
                                            <h4 class="font-bold text-slate-700">{{ $groupName }}</h4>
                                            <div class="group-dropzone min-h-[100px] mt-3 p-2 rounded-xl bg-slate-50" data-zone="{{ $groupName }}"></div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="text-xs text-slate-500">Item yang belum cocok bisa tetap di Bank Item.</div>

                                <div class="hidden-inputs"></div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <div class="flex justify-end">
            <button type="submit" class="px-6 sm:px-8 py-3.5 bg-indigo-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-indigo-700 transition shadow-lg shadow-indigo-200 min-h-[44px] flex items-center justify-center gap-2">
                Kumpulkan Jawaban
            </button>
        </div>
    </form>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // wire drag-drop reorder lists
            document.querySelectorAll('.drag-drop-container').forEach(container => {
                const list = container.querySelector('.dd-list');

                // up/down buttons
                list.addEventListener('click', function (e) {
                    if (e.target.classList.contains('btn-up') || e.target.closest('.btn-up')) {
                        const item = e.target.closest('.dd-item');
                        if (item && item.previousElementSibling) {
                            list.insertBefore(item, item.previousElementSibling);
                        }
                    }
                    if (e.target.classList.contains('btn-down') || e.target.closest('.btn-down')) {
                        const item = e.target.closest('.dd-item');
                        if (item && item.nextElementSibling) {
                            list.insertBefore(item.nextElementSibling, item);
                        }
                    }
                });

                // drag & drop
                let dragged = null;
                list.querySelectorAll('.dd-item').forEach(li => {
                    li.addEventListener('dragstart', function (ev) { dragged = li; ev.dataTransfer.effectAllowed = 'move'; });
                });
                list.addEventListener('dragover', function (ev) { ev.preventDefault(); });
                list.addEventListener('drop', function (ev) {
                    ev.preventDefault();
                    const target = ev.target.closest('.dd-item');
                    if (!dragged) return;
                    if (target && target !== dragged) {
                        list.insertBefore(dragged, target.nextElementSibling);
                    }
                });
            });

            // wire grouping drag & drop
            document.querySelectorAll('.grouping-container').forEach(container => {
                let draggedEl = null;
                container.querySelectorAll('.group-item').forEach(item => {
                    item.addEventListener('dragstart', function (e) { draggedEl = item; e.dataTransfer.effectAllowed = 'move'; });
                });
                container.querySelectorAll('.group-dropzone').forEach(zone => {
                    zone.addEventListener('dragover', function (e) { e.preventDefault(); });
                    zone.addEventListener('drop', function (e) {
                        e.preventDefault();
                        if (!draggedEl) return;
                        zone.appendChild(draggedEl);
                    });
                });
            });

            // on submit, serialize lists and grouping into hidden inputs
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function () {
                    // serialize drag-drop lists
                    document.querySelectorAll('.drag-drop-container').forEach(container => {
                        const qid = container.dataset.questionId;
                        const items = Array.from(container.querySelectorAll('.dd-list .dd-item')).map(li => li.querySelector('.dd-label').textContent.trim());
                        const hidden = container.querySelector('.dd-hidden-input');
                        if (hidden) hidden.value = items.join(', ');
                    });

                    // serialize grouping containers
                    document.querySelectorAll('.grouping-container').forEach(container => {
                        const qid = container.dataset.questionId;
                        const hiddenWrap = container.querySelector('.hidden-inputs');
                        if (!hiddenWrap) return;
                        hiddenWrap.innerHTML = '';

                        container.querySelectorAll('.group-column').forEach(col => {
                            const groupName = col.dataset.groupName || '';
                            col.querySelectorAll('.group-item').forEach(itemEl => {
                                const value = itemEl.dataset.item || itemEl.textContent.trim();
                                const hi = document.createElement('input');
                                hi.type = 'hidden'; hi.name = `answers[${qid}][item][]`; hi.value = value; hiddenWrap.appendChild(hi);
                                const hg = document.createElement('input');
                                hg.type = 'hidden'; hg.name = `answers[${qid}][group][]`; hg.value = groupName; hiddenWrap.appendChild(hg);
                            });
                        });

                        const bankZone = container.querySelector('.group-dropzone[data-zone="bank"]');
                        if (bankZone) {
                            bankZone.querySelectorAll('.group-item').forEach(itemEl => {
                                const value = itemEl.dataset.item || itemEl.textContent.trim();
                                const hi = document.createElement('input');
                                hi.type = 'hidden'; hi.name = `answers[${qid}][item][]`; hi.value = value; hiddenWrap.appendChild(hi);
                                const hg = document.createElement('input');
                                hg.type = 'hidden'; hg.name = `answers[${qid}][group][]`; hg.value = ''; hiddenWrap.appendChild(hg);
                            });
                        }
                    });
                });
            }
        });
    </script>
@endsection
